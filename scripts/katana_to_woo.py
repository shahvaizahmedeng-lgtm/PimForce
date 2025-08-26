#!/usr/bin/env python3
"""
KatanaPIM -> WooCommerce sync (Python)

- Reads active integrations from database table `integrations`
- For each integration, fetches products from KatanaPIM
- Upserts into WooCommerce using SKU as the unique identifier

Environment variables (defaults shown):
  DB_CONNECTION=postgres   # postgres or mysql
  DB_HOST=127.0.0.1
  DB_PORT=5432             # 5432 for postgres, 3306 for mysql
  DB_USERNAME=postgres
  DB_PASSWORD=1234
  DB_DATABASE=PIMFORCE
  DB_SSLMODE=prefer        # postgres only
  SSL_VERIFY=true            # Set to false to disable HTTPS verification (dev only)
  REQUEST_TIMEOUT=120        # HTTP timeout in seconds

Usage examples:
  python3 scripts/katana_to_woo.py --dry-run
  python3 scripts/katana_to_woo.py --integration-id 1

Notes:
- The script supports two possible integration schemas:
  1) Column-based (e.g., `katana_pim_url`, `webshop_url`, ...)
  2) JSON column-based (`apiDetails`, `store_details`, ...)
- The unique identifier is expected to be SKU (`SKU-1`).
"""

from __future__ import annotations

import os
import sys
import json
import time
import argparse
import logging
from typing import Any, Dict, Iterable, List, Optional, Tuple
from datetime import datetime

# Optional .env support
try:
	from dotenv import load_dotenv
	load_dotenv()
except Exception:
	pass

import psycopg
from psycopg.rows import dict_row
import requests

# Optional MySQL support
try:
	import pymysql  # type: ignore
	from pymysql.cursors import DictCursor as MySqlDictCursor  # type: ignore
	_HAS_PYMYSQL = True
except Exception:
	_HAS_PYMYSQL = False


# ---------------------- Logging ----------------------

def get_logger() -> logging.Logger:
	logger = logging.getLogger("katana_to_woo")
	if not logger.handlers:
		logger.setLevel(logging.INFO)
		handler = logging.StreamHandler(sys.stdout)
		handler.setFormatter(logging.Formatter("[%(asctime)s] %(levelname)s: %(message)s"))
		logger.addHandler(handler)
	return logger


log = get_logger()


# ---------------------- Config / DB ----------------------

def get_db_conn():
	"""Create a DB connection for either Postgres or MySQL based on DB_CONNECTION."""
	driver = (os.environ.get("DB_CONNECTION", "postgres") or "postgres").lower()
	host = os.environ.get("DB_HOST", "127.0.0.1")
	user = os.environ.get("DB_USERNAME", "postgres")
	password = os.environ.get("DB_PASSWORD", "1234")
	dbname = os.environ.get("DB_DATABASE", "PIMFORCE")

	if driver in ("mysql", "mariadb"):
		if not _HAS_PYMYSQL:
			raise RuntimeError("PyMySQL is required for DB_CONNECTION=mysql. Please install 'PyMySQL'.")
		port = int(os.environ.get("DB_PORT", "3306"))
		return pymysql.connect(
			host=host,
			port=port,
			user=user,
			password=password,
			database=dbname,
			charset="utf8mb4",
			cursorclass=MySqlDictCursor,
			autocommit=True,
		)
	else:
		port = int(os.environ.get("DB_PORT", "5432"))
		sslmode = os.environ.get("DB_SSLMODE", "prefer")
		conninfo = f"host={host} port={port} user={user} password={password} dbname={dbname} sslmode={sslmode}"
		return psycopg.connect(conninfo)


def _open_dict_cursor(conn):
	"""Return a cursor that yields dict rows for both Postgres and MySQL connections."""
	# psycopg (v3)
	try:
		return conn.cursor(row_factory=dict_row)
	except TypeError:
		pass
	# PyMySQL
	try:
		return conn.cursor(MySqlDictCursor)
	except Exception:
		pass
	# Fallback to default cursor
	return conn.cursor()


def fetch_integrations(conn, integration_id: Optional[int]) -> List[Dict[str, Any]]:
	# Avoid Postgres schema-qualification so the query works on MySQL too
	query = "SELECT * FROM integrations WHERE status = 'active'"
	params: Tuple[Any, ...] = ()
	if integration_id is not None:
		query += " AND id = %s"
		params = (integration_id,)
	with _open_dict_cursor(conn) as cur:
		cur.execute(query, params)
		rows = cur.fetchall()
	return rows


# ---------------------- Utilities ----------------------

def env_bool(name: str, default: bool) -> bool:
	val = os.environ.get(name)
	if val is None:
		return default
	return str(val).lower() in {"1", "true", "yes", "on"}


def get_path(data: Any, path: str, default: Any = None) -> Any:
	"""Nested dict list lookup by dot path, e.g. 'a.b.0.c'"""
	try:
		cur = data
		for seg in path.split('.'):
			if isinstance(cur, list):
				idx = int(seg)
				cur = cur[idx]
			elif isinstance(cur, dict):
				if seg not in cur:
					return default
				cur = cur[seg]
			else:
				return default
		return cur
	except Exception:
		return default


def coalesce(*values: Any) -> Any:
	for v in values:
		if v not in (None, ""):
			return v
	return None


# ---------------------- Integration mapping ----------------------

def extract_integration_config(row: Dict[str, Any]) -> Dict[str, Any]:
	"""Normalize integration row into a common config dict."""
	api_details = row.get("api_data") or row.get("apDetails") or {}
	store_details = row.get("store_data") or {}
	unique_identifier = (
		row.get("unique_identifier")
		or (row.get("uniqueIdentifier") or {}).get("identificationType")
	)

	cfg = {
		"id": row.get("id"),
		"katana_url": coalesce(row.get("katanaPimUrl"), (api_details or {}).get("katanaPimUrl")),
		"katana_api_key": coalesce(api_details.get("katanaPimApiKey"), (api_details or {}).get("katanaApiKey")),
		"webshop_url": coalesce(row.get("webshopUrl"), (api_details or {}).get("webshopUrl")),
		"woo_key": coalesce(api_details.get("wooCommerceApiKey"), (api_details or {}).get("wooApiKey")),
		"woo_secret": coalesce(api_details.get("wooCommerceApiSecret"), (api_details or {}).get("wooApiSecret")),
		"selected_store": row.get("store_data"),
		"store_id": store_details.get("Id"),
		# "unique_identifier": "SKU-1",
		"field_mappings": row.get("fields_mapping_data"),
		"seo_data": row.get("seo_data"),
		"specifications": row.get("specifications"),
		"unique_identifier": (unique_identifier.get("identifier") if isinstance(unique_identifier, dict) else unique_identifier) or "SKU-1",
	}

	# Ensure Katana endpoint ends with /api/v1/product
	if cfg["katana_url"]:
		base = cfg["katana_url"].rstrip('/')
		if not base.endswith('/api/v1/product'):
			base = base + f'/api/v1/product?storeId={cfg["store_id"]}'
		cfg["katana_url"] = base

	return cfg


# ---------------------- KatanaPIM ----------------------

def katana_session(api_key: str) -> requests.Session:
	s = requests.Session()
	s.headers.update({"Accept": "application/json", "ApiKey": api_key})
	verify = env_bool("SSL_VERIFY", True)
	s.verify = verify
	return s


def fetch_katana_products(cfg: Dict[str, Any], timeout: int) -> List[Dict[str, Any]]:
	if not cfg.get("katana_url") or not cfg.get("katana_api_key"):
		raise RuntimeError("Missing KatanaPIM URL or API key in integration config")

	s = katana_session(cfg["katana_api_key"])
	url = cfg["katana_url"]
	page_index = 0
	page_size = 50
	all_items: List[Dict[str, Any]] = []

	while True:
		# params = {"PageIndex": page_index, "PageSize": page_size}
		params = {}
		store_id = cfg.get("selected_store") or cfg.get("store_id")
		if store_id:
			try:
				params["StoreId"] = int(store_id)
			except Exception:
				pass

		log.info(f"Fetching Katana products page={page_index}")
		resp = s.get(url, params=params, timeout=timeout)
		if resp.status_code != 200:
			raise RuntimeError(f"Katana GET failed: {resp.status_code} {resp.text[:500]}")
		js = resp.json()
		items = js.get("Items") or js.get("items") or []
		if not items:
			break
		all_items.extend(items)
		page_index += 1
		total_pages = int(js.get("TotalPages") or js.get("totalPages") or 0)
		if total_pages and page_index >= total_pages:
			break
		if len(items) < page_size:
			break

	log.info(f"Fetched {len(all_items)} product(s) from Katana")
	return all_items


# ---------------------- WooCommerce ----------------------

def woo_session(key: str, secret: str) -> requests.Session:
	s = requests.Session()
	s.auth = (key, secret)
	s.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
	verify = env_bool("SSL_VERIFY", True)
	s.verify = verify
	return s


def find_woo_product_by_sku(s: requests.Session, base_url: str, sku: str, timeout: int) -> Optional[Dict[str, Any]]:
	url = base_url.rstrip('/') + '/wp-json/wc/v3/products'
	resp = s.get(url, params={"sku": sku}, timeout=timeout)
	if resp.status_code == 200:
		arr = resp.json()
		return arr[0] if isinstance(arr, list) and arr else None
	return None


def create_woo_product(s: requests.Session, base_url: str, product: Dict[str, Any], timeout: int) -> Dict[str, Any]:
	url = base_url.rstrip('/') + '/wp-json/wc/v3/products'
	resp = s.post(url, data=json.dumps(product), timeout=timeout)
	if resp.status_code not in (200, 201):
		raise RuntimeError(f"Woo create failed: {resp.status_code} {resp.text[:500]}")
	return resp.json()


def update_woo_product(s: requests.Session, base_url: str, product_id: int, product: Dict[str, Any], timeout: int) -> Dict[str, Any]:
	url = base_url.rstrip('/') + f'/wp-json/wc/v3/products/{product_id}'
	resp = s.put(url, data=json.dumps(product), timeout=timeout)
	if resp.status_code not in (200, 201):
		raise RuntimeError(f"Woo update failed: {resp.status_code} {resp.text[:500]}")
	return resp.json()


# ---------------------- Mapping ----------------------

def derive_sku(product: Dict[str, Any], unique_identifier: str) -> Optional[str]:
	# For SKU-1 we look up common locations in Katana structure
	if unique_identifier and unique_identifier.upper().startswith("SKU"):
		return (
			get_path(product, 'TextFieldsModel.Sku')
			or get_path(product, 'TextFieldsModel.SKU')
			or get_path(product, 'TextFieldsModel.SKU-1')
		)
	return None


def get_mapped_field_value(product: Dict[str, Any], field_mappings: Dict[str, str], woo_field: str) -> Any:
	"""Get field value from product using field mappings configuration."""
	if not field_mappings:
		return None
	
	# Find the Katana field name that maps to this WooCommerce field
	katana_field = None
	for katana_key, woo_key in field_mappings.items():
		if woo_key == woo_field:
			katana_field = katana_key
			break
	
	if not katana_field:
		return None
	
	# Try to get the value from common Katana field locations
	value = (
		get_path(product, f'TextFieldsModel.{katana_field}')
		or get_path(product, f'TextFields.{katana_field}')
		or get_path(product, katana_field)
		or product.get(katana_field)
	)
	
	return value


def map_product_to_woo(cfg: Dict[str, Any], product: Dict[str, Any]) -> Dict[str, Any]:
	log.info(f"map_product_to_woo: {cfg}")
	
	field_mappings = cfg.get('field_mappings', {})
	
	# Get mapped field values
	mapped_name = get_mapped_field_value(product, field_mappings, 'Title')
	name = mapped_name or get_path(product, 'TextFieldsModel.Name') or str(product.get('Id', 'Unknown Product'))
	
	mapped_sku = get_mapped_field_value(product, field_mappings, 'SKU')
	sku = mapped_sku or derive_sku(product, cfg.get('unique_identifier') or 'SKU-1') or ''
	
	description = get_path(product, 'TextFieldsModel.FullDescription') or ''
	short_desc = get_path(product, 'TextFieldsModel.ShortDescription') or ''

	regular_price = get_path(product, 'Prices.CurrentPriceBookItem.Price')
	if not isinstance(regular_price, (int, float, str)):
		regular_price = get_path(product, 'Prices.PriceBookItems.0.Price')
	sale_price = get_path(product, 'Prices.SpecialPrice')

	stock_qty = int(get_path(product, 'Stock.TotalStock') or 0)
	weight = get_path(product, 'Dimensions.Weight')
	length = get_path(product, 'Dimensions.Length')
	width = get_path(product, 'Dimensions.Width')
	height = get_path(product, 'Dimensions.Height')

	# Categories
	categories: List[Dict[str, Any]] = []
	for cat in get_path(product, 'Collections.Categories', []) or []:
		if isinstance(cat, dict):
			name_val = cat.get('Name')
			if name_val:
				categories.append({"name": str(name_val)})
		else:
			categories.append({"name": str(cat)})

	# --- Add categories from cfg["specifications"] if present in product ---
	specs = cfg.get("specifications")
	if specs:
		# Parse if it's a JSON string
		if isinstance(specs, str):
			try:
				specs = json.loads(specs)
			except Exception:
				specs = []
		# Ensure it's a list
		if isinstance(specs, dict):
			specs = [specs]
		if isinstance(specs, list):
			product_specs = get_path(product, "Collections.Specs", []) or []
			for spec in specs:
				spec_id = spec.get("Id")
				for prod_spec in product_specs:
					if isinstance(prod_spec, dict) and prod_spec.get("Id") == spec_id:
						category_obj = {
							"id": spec_id,
							"name": spec.get("Name"),
							"slug": spec.get("Code"),
						}
						if category_obj not in categories:
							categories.append(category_obj)

	# Attributes (GTIN + specs)
	attributes: List[Dict[str, Any]] = []
	gtin = get_mapped_field_value(product, field_mappings, 'GTIN') or get_path(product, 'TextFieldsModel.Gtin')
	if gtin:
		attributes.append({"name": "GTIN", "visible": True, "variation": False, "options": [str(gtin)]})
	for spec in get_path(product, 'Collections.Specs', []) or []:
		if isinstance(spec, dict):
			name_val = spec.get('Name')
			option = spec.get('OptionName')
			if name_val and option:
				attributes.append({
					"name": str(name_val),
					"visible": True,
					"variation": False,
					"options": [str(option)],
				})

	meta_data: List[Dict[str, Any]] = []
	if 'Id' in product:
		meta_data.append({"key": "katana_id", "value": str(product['Id'])})
	if 'ExternalKey' in product:
		meta_data.append({"key": "katana_external_key", "value": str(product['ExternalKey'])})
	if gtin:
		meta_data.append({"key": "gtin", "value": str(gtin)})

	woo_product = {
		"name": str(name),
		"type": "simple",
		"status": "publish",
		"catalog_visibility": "visible",
		"description": str(description),
		"short_description": str(short_desc),
		"sku": str(sku),
		"regular_price": str(regular_price) if regular_price is not None else None,
		"sale_price": str(sale_price) if sale_price is not None else None,
		"manage_stock": True,
		"stock_quantity": stock_qty,
		"stock_status": "instock" if stock_qty > 0 else "outofstock",
		"weight": str(weight) if weight is not None else None,
		"dimensions": {
			"length": str(length) if length is not None else None,
			"width": str(width) if width is not None else None,
			"height": str(height) if height is not None else None,
		},
		"categories": categories,
		"attributes": attributes,
		"meta_data": meta_data,
	}

	# Remove null/empty fields recursively where appropriate
	def _clean(obj: Any) -> Any:
		if isinstance(obj, dict):
			return {k: _clean(v) for k, v in obj.items() if v not in (None, "")}
		if isinstance(obj, list):
			return [
				_clean(v) for v in obj
				if not (v is None or (isinstance(v, str) and v == "") or (isinstance(v, dict) and not v) or (isinstance(v, list) and not v))
			]
		return obj

	return _clean(woo_product)


# ---------------------- Main flow ----------------------

def process_integration(cfg: Dict[str, Any], dry_run: bool, timeout: int) -> Tuple[int, int]:
	if not cfg.get("webshop_url") or not cfg.get("woo_key") or not cfg.get("woo_secret"):
		raise RuntimeError("Missing WooCommerce credentials in integration config")

	products = fetch_katana_products(cfg, timeout)
	woo = woo_session(cfg["woo_key"], cfg["woo_secret"])
	base = cfg["webshop_url"]

	success = 0
	errors = 0
	total_products = len(products)
	
	log.info(f"Starting sync of {total_products} products from Katana to WooCommerce")
	
	for i, prod in enumerate(products, 1):
		try:
			sku = derive_sku(prod, cfg.get("unique_identifier") or "SKU-1")
			if not sku:
				log.warning(f"Product {i}/{total_products} missing SKU; skipping")
				continue

			payload = map_product_to_woo(cfg, prod)
			if dry_run:
				log.info(f"DRY-RUN [{i}/{total_products}] would upsert SKU={sku}: {json.dumps(payload)[:300]}...")
				success += 1
				continue

			existing = find_woo_product_by_sku(woo, base, sku, timeout)
			if existing:
				pid = int(existing.get('id'))
				update_woo_product(woo, base, pid, payload, timeout)
				log.info(f"[{i}/{total_products}] Updated Woo product id={pid} SKU={sku}")
			else:
				created = create_woo_product(woo, base, payload, timeout)
				log.info(f"[{i}/{total_products}] Created Woo product id={created.get('id')} SKU={sku}")
			success += 1
		except Exception as e:
			errors += 1
			log.error(f"[{i}/{total_products}] Failed to upsert product SKU={derive_sku(prod, cfg.get('unique_identifier') or 'SKU-1')}: {e}")
	
	log.info(f"Sync completed for integration {cfg['id']}: {success} successful, {errors} errors out of {total_products} total products")
	return success, errors


def main() -> int:
	parser = argparse.ArgumentParser(description="Sync products from KatanaPIM to WooCommerce using SKU unique id")
	parser.add_argument("--integration-id", type=int, default=None, help="Only process a specific integration id")
	parser.add_argument("--dry-run", action="store_true", help="Do not write to WooCommerce; only log actions")
	parser.add_argument("--verbose", "-v", action="store_true", help="Verbose logging")
	args = parser.parse_args()

	if args.verbose:
		log.setLevel(logging.DEBUG)

	timeout = int(os.environ.get("REQUEST_TIMEOUT", "120"))

	try:
		with get_db_conn() as conn:
			rows = fetch_integrations(conn, args.integration_id)
			if not rows:
				log.warning("No active integrations found")
				return 0

			total_ok = 0
			total_err = 0
			total_integrations = len(rows)
			
			log.info(f"Starting sync process for {total_integrations} integration(s)")
			
			for i, row in enumerate(rows, 1):
				log.info(f"Processing row: {row}")
				log.info(f"Processing integration {i}/{total_integrations}: id={row['id']}")
				cfg = extract_integration_config(row)
				log.info(f"Configuration: katana_url={cfg.get('katana_url')} webshop={cfg.get('webshop_url')}")
				try:
					s_ok, s_err = process_integration(cfg, args.dry_run, timeout)
					total_ok += s_ok
					total_err += s_err
				except Exception as e:
					log.error(f"Integration id={cfg['id']} failed: {e}")
					total_err += 1

			log.info(f"All integrations completed! Total products synced: {total_ok} successful, {total_err} errors")
			log.info(f"Summary: {total_ok + total_err} total products processed across {total_integrations} integration(s)")
			
			if total_err == 0:
				log.info("All products successfully synced to WooCommerce!")
			else:
				log.warning(f"⚠️  {total_err} products failed to sync. Check logs for details.")
			
			return 0 if total_err == 0 else 2
	except Exception as e:
		log.error(f"Fatal error: {e}")
		return 1


if __name__ == "__main__":
	sys.exit(main())