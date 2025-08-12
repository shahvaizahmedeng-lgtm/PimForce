# KatanaPIM to WooCommerce Product Sync

This document explains how to use the product synchronization script that transfers products from KatanaPIM to WooCommerce based on integration configurations.

## Overview

The sync script (`integrations:sync-products`) automatically fetches products from KatanaPIM and creates/updates them in WooCommerce based on the field mappings defined in your integration configurations.

## Features

- **Automatic Field Mapping**: Maps KatanaPIM fields to WooCommerce fields based on integration configuration
- **Smart Updates**: Updates existing products or creates new ones based on SKU matching
- **Error Handling**: Comprehensive error handling and logging
- **Dry Run Mode**: Test sync without making actual changes
- **Scheduled Execution**: Can run automatically every 15 minutes
- **Selective Sync**: Sync specific integrations by ID

## Prerequisites

1. **Active Integration**: Ensure you have an active integration configured with:
   - KatanaPIM URL and API Key
   - WooCommerce URL, API Key, and API Secret
   - Field mappings configured

2. **WooCommerce REST API**: Ensure WooCommerce REST API is enabled on your store

3. **Valid API Credentials**: Test your API connections before running the sync

## Usage

### Basic Sync (All Active Integrations)

```bash
php artisan integrations:sync-products
```

### Dry Run (Test Mode)

Test what would be synced without making actual changes:

```bash
php artisan integrations:sync-products --dry-run
```

### Sync Specific Integration

Sync only a specific integration by ID:

```bash
php artisan integrations:sync-products --integration-id=1
```

### Dry Run for Specific Integration

```bash
php artisan integrations:sync-products --integration-id=1 --dry-run
```

## Field Mapping

The script maps the following fields based on your integration configuration:

### Basic Product Fields
- **Name**: `field_name` → WooCommerce product name
- **Description**: `field_long_description` → WooCommerce product description
- **Short Description**: `field_short_description` → WooCommerce short description
- **SKU**: `unique_identifier` → WooCommerce SKU
- **Price**: `price` → WooCommerce regular price
- **Sale Price**: `sale_price` → WooCommerce sale price
- **Stock Quantity**: `stock_quantity` → WooCommerce stock quantity

### Additional Fields
- **GTIN**: `field_gtin` → WooCommerce product attribute
- **Tax Category**: `field_tax_category` → WooCommerce product attribute
- **Weight**: `weight` → WooCommerce product weight
- **Dimensions**: `length`, `width`, `height` → WooCommerce product dimensions

### Categories
- **Category Mapping**: `select_value_1` through `select_value_14` → WooCommerce product categories

### SEO Fields
- **Meta Title**: `meta_title` → Yoast SEO title
- **Meta Description**: `meta_description` → Yoast SEO meta description
- **Keywords**: `keywords` → Yoast SEO focus keyword

### Product Condition
- **Condition**: `condition` and `condition_value` → WooCommerce product meta data

## Scheduled Execution

The sync script is configured to run automatically every 15 minutes. You can modify the schedule in `app/Console/Kernel.php`:

```php
$schedule->command('integrations:sync-products')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/sync-products.log'));
```

### Manual Schedule Execution

To run the scheduler manually:

```bash
php artisan schedule:run
```

## Logging

The sync script logs all activities to:

- **Console Output**: Real-time progress and errors
- **Laravel Logs**: `storage/logs/laravel.log`
- **Sync Logs**: `storage/logs/sync-products.log` (when run via scheduler)

## Error Handling

The script handles various error scenarios:

1. **Invalid Integration Configuration**: Skips integrations with missing required fields
2. **API Connection Errors**: Logs connection failures and continues with other integrations
3. **Product Processing Errors**: Continues processing other products if one fails
4. **WooCommerce API Errors**: Logs detailed error messages for debugging

## Troubleshooting

### Common Issues

1. **SSL Certificate Errors**
   - Ensure your KatanaPIM and WooCommerce URLs use valid SSL certificates
   - For development, you may need to configure SSL verification settings

2. **API Authentication Errors**
   - Verify your API keys and secrets are correct
   - Ensure WooCommerce REST API is enabled
   - Check API key permissions in WooCommerce

3. **Field Mapping Issues**
   - Verify field names in KatanaPIM match your integration configuration
   - Check that required fields are properly mapped

4. **Product Creation/Update Failures**
   - Check WooCommerce product limits and settings
   - Verify product data format and required fields

### Debug Mode

Enable verbose output for debugging:

```bash
php artisan integrations:sync-products -v
```

### Check Logs

Review sync logs for detailed error information:

```bash
tail -f storage/logs/sync-products.log
```

## API Endpoints

### KatanaPIM API
- **Endpoint**: `{katana_pim_url}/api/products`
- **Method**: GET
- **Authentication**: Bearer token
- **Headers**: Content-Type: application/json, Accept: application/json

### WooCommerce API
- **Endpoint**: `{webshop_url}/wp-json/wc/v3/products`
- **Method**: GET, POST, PUT
- **Authentication**: Basic Auth (API Key + API Secret)

## Security Considerations

1. **API Keys**: Store API keys securely in the database
2. **SSL**: Always use HTTPS for API communications
3. **Permissions**: Use API keys with minimal required permissions
4. **Logging**: Be careful with sensitive data in logs

## Performance

- **Batch Processing**: Products are processed one by one to avoid overwhelming APIs
- **Error Isolation**: Individual product failures don't stop the entire sync
- **Background Execution**: Scheduled runs execute in the background
- **Overlap Prevention**: Prevents multiple sync processes from running simultaneously

## Customization

You can customize the sync behavior by modifying the `SyncProductsCommand` class:

- **Field Mapping Logic**: Modify `mapProductData()` method
- **API Endpoints**: Update API URLs in fetch methods
- **Error Handling**: Customize error handling in `processIntegration()` method
- **Logging**: Add custom logging in any method

## Support

For issues or questions about the product sync:

1. Check the logs for error details
2. Verify your integration configuration
3. Test API connections manually
4. Review this documentation

## Example Integration Configuration

```php
// Example integration fields
$integration = [
    'katana_pim_url' => 'https://your-katana-instance.com',
    'katana_pim_api_key' => 'your-katana-api-key',
    'webshop_url' => 'https://your-woocommerce-store.com',
    'woo_commerce_api_key' => 'your-woo-api-key',
    'woo_commerce_api_secret' => 'your-woo-api-secret',
    'unique_identifier' => 'sku',
    'field_name' => 'name',
    'field_long_description' => 'description',
    'field_short_description' => 'short_description',
    'field_gtin' => 'gtin',
    'field_tax_category' => 'tax_category',
    'meta_title' => 'seo_title',
    'meta_description' => 'seo_description',
    'keywords' => 'seo_keywords',
    'condition' => 'product_condition',
    'condition_value' => 'new',
    'select_value_1' => 'category',
    'select_value_2' => 'brand',
    // ... additional field mappings
];
```
