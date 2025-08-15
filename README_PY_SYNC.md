### KatanaPIM → WooCommerce Python Sync

Run a Python script that reads `public\integrations` from PostgreSQL and upserts WooCommerce products using SKU.

Prerequisites:
- Python 3.10+
- Access to PostgreSQL and WooCommerce API

Install:

```bash
python3 -m pip install -r requirements.txt
```

Env vars (defaults shown):
- `DB_HOST=127.0.0.1`
- `DB_PORT=5432`
- `DB_USERNAME=postgres`
- `DB_PASSWORD=1234`
- `DB_DATABASE=PIMFORCE`
- `DB_SSLMODE=prefer`
- `SSL_VERIFY=true` (set `false` for dev/self-signed)
- `REQUEST_TIMEOUT=120`

Usage examples:

```bash
python3 scripts/katana_to_woo.py --dry-run
python3 scripts/katana_to_woo.py --integration-id 1
```

Notes:
- The script understands both column-style configs (e.g., `katana_pim_url`) and JSON-style (`apiDetails`, `store_details`).
- Unique identifier is SKU (`SKU-1`), the product is updated if present, created otherwise.