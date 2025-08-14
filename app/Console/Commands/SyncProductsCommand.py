#!/usr/bin/env python3
"""
Usage (env-driven):
  TABLES="public.users,orders" UPDATED_SINCE_MINUTES=15 OUTPUT_DIR="/home/forge/your-domain/storage/app/exports" \
  DATABASE_URL="postgresql://user:pass@host:5432/dbname" \
  ./pg_fetch_tables.py

If DATABASE_URL is not set, set PGHOST, PGPORT, PGUSER, PGPASSWORD, PGDATABASE (and optionally PGSSLMODE).
"""

import os
import sys
import json
import logging
from pathlib import Path
from datetime import datetime, timedelta, timezone

# Optional .env support (won’t crash if missing)
try:
    from dotenv import load_dotenv
    load_dotenv()  # loads from nearest .env
except Exception:
    pass

import psycopg
from psycopg import sql
from psycopg.rows import dict_row


def get_logger() -> logging.Logger:
    log = logging.getLogger("pg_fetch_tables")
    log.setLevel(logging.INFO)
    handler = logging.StreamHandler(sys.stdout)
    handler.setFormatter(logging.Formatter("[%(asctime)s] %(levelname)s: %(message)s"))
    log.addHandler(handler)
    return log


log = get_logger()


def get_conn():
    # db_url = os.environ.get("DATABASE_URL" , "")
    # if db_url:
    #     return psycopg.connect(db_url)
    # Fallback to discrete vars
    host = os.environ.get("DB_HOST", "127.0.0.1")
    port = int(os.environ.get("DB_PORT", "5432"))
    user = os.environ.get("DB_USERNAME", "postgres")
    password = os.environ.get("DB_PASSWORD", "1234")
    dbname = os.environ.get("DB_DATABASE", "PIMFORCE")
    sslmode = os.environ.get("DB_SSLMODE", "prefer")

    if not all([user, password, dbname]):
        raise RuntimeError("Missing DB config. Set DATABASE_URL or PGUSER/PGPASSWORD/PGDATABASE.")

    conninfo = f"host={host} port={port} user={user} password={password} dbname={dbname} sslmode={sslmode}"
    return psycopg.connect(conninfo)


def split_table_name(name: str):
    name = name.strip()
    if not name:
        raise ValueError("Empty table name.")
    parts = name.split(".", 1)
    if len(parts) == 1:
        return "public", parts[0]
    return parts[0], parts[1]


def has_column(conn, schema: str, table: str, column: str) -> bool:
    q = """
      select 1
      from information_schema.columns
      where table_schema = %s and table_name = %s and column_name = %s
      limit 1
    """
    with conn.cursor() as cur:
        cur.execute(q, (schema, table, column))
        return cur.fetchone() is not None


def fetch_table(conn, schema: str, table: str, updated_since: datetime | None, limit: int):
    """Return (rows, used_filter) where rows is a list of dicts."""
    use_updated_filter = False
    where_clause = sql.SQL("")
    order_clause = sql.SQL("")
    params = []

    if updated_since and has_column(conn, schema, table, "updated_at"):
        where_clause = sql.SQL(" WHERE {} >= %s").format(sql.Identifier("updated_at"))
        order_clause = sql.SQL(" ORDER BY {} ASC").format(sql.Identifier("updated_at"))
        params.append(updated_since)
        use_updated_filter = True

    query = sql.SQL("SELECT * FROM {}.{}").format(sql.Identifier(schema), sql.Identifier(table))
    query = sql.SQL("").join([query, where_clause, order_clause, sql.SQL(" LIMIT %s")])
    params.append(limit)

    with conn.cursor(row_factory=dict_row) as cur:
        cur.execute(query, params)
        rows = cur.fetchall()
    return rows, use_updated_filter


def main():
    tables_raw = os.environ.get("TABLES", "public.integrations")
    if not tables_raw:
        log.error("Set TABLES env var, e.g. TABLES='public.users,public.orders'")
        sys.exit(2)

    try:
        limit = int(os.environ.get("LIMIT", "10000"))
    except ValueError:
        limit = 10000

    out_dir = Path(os.environ.get("OUTPUT_DIR", Path(__file__).resolve().parent / "output"))
    out_dir.mkdir(parents=True, exist_ok=True)

    # Optional incremental filter
    mins = int(os.environ.get("UPDATED_SINCE_MINUTES", "0"))
    updated_since = None
    if mins > 0:
        updated_since = datetime.now(timezone.utc) - timedelta(minutes=mins)

    tables = [t for t in (s.strip() for s in tables_raw.split(",")) if t]

    total_rows = 0
    failures = 0

    try:
        with get_conn() as conn:
            for tname in tables:
                try:
                    schema, table = split_table_name(tname)
                    log.info(f"Fetching from {schema}.{table} (limit={limit}, "
                             f"updated_since={updated_since.isoformat() if updated_since else 'None'})")

                    rows, used_filter = fetch_table(conn, schema, table, updated_since, limit)
                    count = len(rows)
                    total_rows += count

                    timestamp = datetime.now(timezone.utc).strftime("%Y%m%d_%H%M%S")
                    outfile = out_dir / f"{schema}.{table}_{timestamp}.jsonl"

                    with outfile.open("w", encoding="utf-8") as f:
                        for r in rows:
                            f.write(json.dumps(r, default=str) + "\n")

                    log.info(f"{schema}.{table}: wrote {count} rows to {outfile} "
                             f"{'(filtered by updated_at)' if used_filter else '(no filter)'}")

                except Exception as e:
                    failures += 1
                    log.error(f"Failed on table '{tname}': {e}", exc_info=True)

    except Exception as e:
        log.error(f"DB connection error: {e}", exc_info=True)
        sys.exit(1)

    if failures:
        log.warning(f"Completed with {failures} table error(s). Total rows: {total_rows}")
        sys.exit(3)

    log.info(f"Done. Total rows across tables: {total_rows}")
    sys.exit(0)


if __name__ == "__main__":
    main()
