<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Integration;
use Exception;

class SyncProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integrations:sync-products {--integration-id= : Sync specific integration by ID} {--dry-run : Show what would be synced without actually syncing} {--test-connection : Test KatanaPIM API connection only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from KatanaPIM to WooCommerce based on integration configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting KatanaPIM to WooCommerce product synchronization...');
        
        try {
            if ($this->option('test-connection')) {
                $this->testConnections();
                return Command::SUCCESS;
            }
            
            $this->processIntegrations();
            $this->info('Product synchronization completed successfully!');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Synchronization failed: ' . $e->getMessage());
            Log::error('Product sync failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return Command::FAILURE;
        }
    }
    
    /**
     * Test API connections for debugging
     */
    private function testConnections()
    {
        $query = Integration::where('status', 'active');
        
        if ($integrationId = $this->option('integration-id')) {
            $query->where('id', $integrationId);
        }
        
        $integrations = $query->get();
        
        if ($integrations->isEmpty()) {
            $this->warn('No active integrations found.');
            return;
        }
        
        foreach ($integrations as $integration) {
            $this->info("\nTesting connection for integration: {$integration->integration_name}");
            $this->testKatanaConnection($integration);
        }
    }
    
    /**
     * Test KatanaPIM API connection
     */
    private function testKatanaConnection(Integration $integration)
    {
        try {
            $baseUrl = rtrim($integration->katana_pim_url, '/');
            if (!str_ends_with($baseUrl, '/api/v1/product')) {
                $baseUrl .= '/api/v1/product';
            }
            
            $this->line("Testing URL: {$baseUrl}");
            $this->line("API Key: " . substr($integration->katana_pim_api_key, 0, 10) . "...");
            
            $http = $this->katanaClient($integration);
            
            $params = [
                'PageIndex' => 0,
                'PageSize'  => 1,
            ];
            
            if (!empty($integration->selected_store)) {
                $params['StoreId'] = (int)$integration->selected_store;
                $this->line("Using StoreId: {$integration->selected_store}");
            } elseif (!empty($integration->store_details) && is_array($integration->store_details)) {
                $storeId = $integration->store_details['id'] ?? null;
                if ($storeId) {
                    $params['StoreId'] = (int)$storeId;
                    $this->line("Using StoreId from store_details: {$storeId}");
                }
            }
            
            $this->line("Making test request...");
            $resp = $http->timeout(60)->get($baseUrl, $params);
            
            $this->line("Response status: {$resp->status()}");
            
            if ($resp->successful()) {
                $json = $resp->json();
                $this->info("✓ KatanaPIM API connection successful!");
                $this->line("Response keys: " . implode(', ', array_keys($json)));
                
                $items = $json['Items'] ?? $json['items'] ?? [];
                $totalCount = $json['TotalCount'] ?? $json['totalCount'] ?? 0;
                
                $this->line("Total products available: {$totalCount}");
                $this->line("Items in response: " . count($items));
                
                if (!empty($items)) {
                    $this->line("Sample product structure:");
                    $this->line(json_encode($items[0], JSON_PRETTY_PRINT));
                }
            } else {
                $this->error("✗ KatanaPIM API connection failed!");
                $this->error("Status: {$resp->status()}");
                $this->error("Response: " . $resp->body());
            }
            
        } catch (Exception $e) {
            $this->error("✗ KatanaPIM API connection failed with exception!");
            $this->error("Error: " . $e->getMessage());
        }
    }
    
    /**
     * Process all active integrations
     */
    private function processIntegrations()
    {
        $query = Integration::where('status', 'active');
        
        // If specific integration ID is provided
        if ($integrationId = $this->option('integration-id')) {
            $query->where('id', $integrationId);
        }
        
        $integrations = $query->get();
        
        if ($integrations->isEmpty()) {
            $this->warn('No active integrations found.');
            return;
        }
        
        $this->info("Found {$integrations->count()} active integration(s) to process.");
        
        foreach ($integrations as $integration) {
            $this->processIntegration($integration);
        }
    }
    
    /**
     * Process a single integration
     */
    private function processIntegration(Integration $integration)
    {
        $this->info("\nProcessing integration: {$integration->integration_name}");
        
        try {
            // Validate integration configuration
            if (!$this->validateIntegration($integration)) {
                $this->error("Integration {$integration->integration_name} has invalid configuration. Skipping.");
                return;
            }
            
            // Fetch products from KatanaPIM
            $katanaProducts = $this->fetchKatanaProducts($integration);
            
            if (empty($katanaProducts)) {
                $this->warn("No products found in KatanaPIM for integration {$integration->integration_name}");
                return;
            }
            
            $this->info("Found " . count($katanaProducts) . " products in KatanaPIM");
            
            // Process each product
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($katanaProducts as $product) {
                try {
                    $this->processProduct($integration, $product);
                    $successCount++;
                } catch (Exception $e) {
                    $errorCount++;
                    $this->error("Failed to process product {$product['id']}: " . $e->getMessage());
                    Log::error('Product processing failed', [
                        'integration_id' => $integration->id,
                        'product_id' => $product['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $this->info("Integration {$integration->integration_name} completed: {$successCount} successful, {$errorCount} failed");
            
        } catch (Exception $e) {
            $this->error("Failed to process integration {$integration->integration_name}: " . $e->getMessage());
            Log::error('Integration processing failed', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Validate integration configuration
     */
    private function validateIntegration(Integration $integration): bool
    {
        $requiredFields = [
            'katana_pim_url',
            'katana_pim_api_key',
            'webshop_url',
            'woo_commerce_api_key',
            'woo_commerce_api_secret'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($integration->$field)) {
                $this->error("Missing required field: {$field}");
                return false;
            }
        }
        
        // Validate URLs
        if (!filter_var($integration->katana_pim_url, FILTER_VALIDATE_URL)) {
            $this->error("Invalid KatanaPIM URL: {$integration->katana_pim_url}");
            return false;
        }
        
        if (!filter_var($integration->webshop_url, FILTER_VALIDATE_URL)) {
            $this->error("Invalid WooCommerce URL: {$integration->webshop_url}");
            return false;
        }
        
        // Check for obvious invalid URLs (like Stripe checkout URLs)
        if (str_contains($integration->katana_pim_url, 'stripe.com') || 
            str_contains($integration->katana_pim_url, 'checkout.stripe.com')) {
            $this->error("Invalid KatanaPIM URL detected (appears to be a Stripe checkout URL): {$integration->katana_pim_url}");
            return false;
        }
        
        // Validate store configuration
        if (empty($integration->selected_store) && empty($integration->store_details)) {
            $this->warn("No store configuration found. This might cause issues with product filtering.");
        }
        
        return true;
    }
    
    /**
     * Create KatanaPIM HTTP client
     */
    private function katanaClient(Integration $integration)
    {
        $http = Http::acceptJson()->withHeaders([
            'ApiKey' => $integration->katana_pim_api_key,
        ]);
        
        if (app()->environment('local', 'development')) {
            $http = $http->withoutVerifying();
        }
        
        // Add debugging for development
        if (app()->environment('local', 'development')) {
            $http = $http->withOptions([
                'debug' => true,
                'verify' => false
            ]);
        }
        
        return $http;
    }
    
    /**
     * Fetch products from KatanaPIM
     */
    private function fetchKatanaProducts(Integration $integration): array
    {
        // Ensure the URL ends with the correct API endpoint
        $baseUrl = rtrim($integration->katana_pim_url, '/');
        if (!str_ends_with($baseUrl, '/api/v1/product')) {
            $baseUrl .= '/api/v1/product';
        }

        $http = $this->katanaClient($integration);

        $pageIndex = 0;
        $pageSize  = 50; // Increased page size for better performance
        $all = [];

        do {
            $params = [
                'PageIndex' => $pageIndex,
                'PageSize'  => $pageSize,
            ];
            
            // Use selected_store if available, otherwise try to extract from store_details
            if (!empty($integration->selected_store)) {
                $params['StoreId'] = (int)$integration->selected_store;
            } elseif (!empty($integration->store_details) && is_array($integration->store_details)) {
                $storeId = $integration->store_details['id'] ?? null;
                if ($storeId) {
                    $params['StoreId'] = (int)$storeId;
                }
            }

            $this->line("Fetching page {$pageIndex} (size: {$pageSize})...");
            $this->line("URL: {$baseUrl}");
            $this->line("Params: " . json_encode($params));
            
            try {
                $resp = $http->timeout(300)->get($baseUrl, $params); // Increased timeout to 5 minutes
                
                if (!$resp->successful()) {
                    $this->error("KatanaPIM GET failed: HTTP {$resp->status()}");
                    $this->error("Response body: " . $resp->body());
                    throw new Exception("KatanaPIM GET failed: HTTP {$resp->status()} " . $resp->body());
                }

                $json = $resp->json();
                
                // Debug the response structure
                $this->line("Response keys: " . implode(', ', array_keys($json)));
                
                $items = $json['Items'] ?? $json['items'] ?? [];
                $totalPages = (int)($json['TotalPages'] ?? $json['totalPages'] ?? 0);

                if (!is_array($items) || empty($items)) {
                    $this->line("No more items found on page {$pageIndex}");
                    break;
                }

                $this->line("Received " . count($items) . " products on page {$pageIndex}");
                $all = array_merge($all, $items);

                $pageIndex++;
                if ($totalPages && $pageIndex >= $totalPages) {
                    $this->line("Reached total pages limit ({$totalPages})");
                    break;
                }
                if (count($items) < $pageSize) {
                    $this->line("Received fewer items than page size, stopping pagination");
                    break;
                }

            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'timed out')) {
                    $this->warn("Timeout on page {$pageIndex}, but we have " . count($all) . " products so far");
                    break;
                }
                throw $e;
            }

        } while (true);

        $this->info("Fetched " . count($all) . " products from KatanaPIM.");
        return $all;
    }
    
    /**
     * Process a single product
     */
    private function processProduct(Integration $integration, array $product)
    {
        $productName = $this->getPath($product, 'TextFieldsModel.Name') ?? ($product['Id'] ?? 'unknown');
        $this->line("Processing product: {$productName}");
        
        // Map product data according to integration configuration
        $wooProduct = $this->mapProductData($integration, $product);
        
        if ($this->option('dry-run')) {
            $this->info("DRY RUN - Would sync product: " . json_encode($wooProduct, JSON_PRETTY_PRINT));
            return;
        }
        
        // Check if product exists in WooCommerce
        $existingProduct = $this->findWooProduct($integration, $product);
        
        if ($existingProduct) {
            $this->updateWooProduct($integration, $existingProduct['id'], $wooProduct);
            $this->line("Updated existing product in WooCommerce");
        } else {
            $this->createWooProduct($integration, $wooProduct);
            $this->line("Created new product in WooCommerce");
        }
    }
    
    /**
     * Get nested value from array using dot notation
     */
    private function getPath(array $arr, string $path, $default = null)
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if (!is_array($arr) || !array_key_exists($segment, $arr)) {
                return $default;
            }
            $arr = $arr[$segment];
        }
        return $arr;
    }
    
    /**
     * Map KatanaPIM product data to WooCommerce format
     */
    private function mapProductData(Integration $integration, array $product): array
    {
        // Extract basic product information using the new structure
        $name = $this->getPath($product, 'TextFieldsModel.Name') ?? 'Unknown Product';
        $description = $this->getPath($product, 'TextFieldsModel.FullDescription') ?? '';
        $shortDescription = $this->getPath($product, 'TextFieldsModel.ShortDescription') ?? '';
        $sku = $this->getPath($product, 'TextFieldsModel.Sku') ?? '';
        
        // Extract pricing information
        $regularPrice = $this->getPath($product, 'Prices.CurrentPriceBookItem.Price');
        if (!is_numeric($regularPrice)) {
            $regularPrice = $this->getPath($product, 'Prices.PriceBookItems.0.Price');
        }
        $salePrice = $this->getPath($product, 'Prices.SpecialPrice');
        
        // Extract stock information
        $stockQuantity = (int)($this->getPath($product, 'Stock.TotalStock') ?? 0);
        
        // Extract dimensions
        $weight = $this->getPath($product, 'Dimensions.Weight');
        $length = $this->getPath($product, 'Dimensions.Length');
        $width = $this->getPath($product, 'Dimensions.Width');
        $height = $this->getPath($product, 'Dimensions.Height');
        
        $wooProduct = [
            'name' => (string)$name,
            'type' => 'simple',
            'status' => 'publish',
            'catalog_visibility' => 'visible',
            'description' => (string)$description,
            'short_description' => (string)$shortDescription,
            'sku' => (string)$sku,
            'regular_price' => is_numeric($regularPrice) ? (string)$regularPrice : null,
            'sale_price' => is_numeric($salePrice) ? (string)$salePrice : null,
            'manage_stock' => true,
            'stock_quantity' => $stockQuantity,
            'stock_status' => $stockQuantity > 0 ? 'instock' : 'outofstock',
            'weight' => isset($weight) ? (string)$weight : null,
            'dimensions' => [
                'length' => isset($length) ? (string)$length : null,
                'width' => isset($width) ? (string)$width : null,
                'height' => isset($height) ? (string)$height : null
            ],
            'categories' => $this->mapCategories($integration, $product),
            'attributes' => $this->mapAttributes($integration, $product),
            'meta_data' => $this->mapMetaData($integration, $product)
        ];
        
        // Add product condition if configured
        if ($integration->condition && $integration->condition_value) {
            $wooProduct['meta_data'][] = [
                'key' => $integration->condition,
                'value' => $integration->condition_value
            ];
        }
        
        // Add SEO meta data if configured
        if ($integration->meta_title) {
            $wooProduct['meta_data'][] = [
                'key' => '_yoast_wpseo_title',
                'value' => $this->getPath($product, $integration->meta_title)
            ];
        }
        
        if ($integration->meta_description) {
            $wooProduct['meta_data'][] = [
                'key' => '_yoast_wpseo_metadesc',
                'value' => $this->getPath($product, $integration->meta_description)
            ];
        }
        
        if ($integration->keywords) {
            $wooProduct['meta_data'][] = [
                'key' => '_yoast_wpseo_focuskw',
                'value' => $this->getPath($product, $integration->keywords)
            ];
        }
        
        // Filter out null and empty values
        return array_filter($wooProduct, function($value) {
            return $value !== null && $value !== '';
        });
    }
    
    /**
     * Get field value from product data
     */
    private function getFieldValue(array $product, string $field, $default = null)
    {
        return $product[$field] ?? $default;
    }
    
    /**
     * Get stock status
     */
    private function getStockStatus(array $product): string
    {
        $stockQuantity = (int)($this->getPath($product, 'Stock.TotalStock') ?? 0);
        return $stockQuantity > 0 ? 'instock' : 'outofstock';
    }
    
    /**
     * Map categories
     */
    private function mapCategories(Integration $integration, array $product): array
    {
        $categories = [];
        
        // Get categories from KatanaPIM structure
        $katanaCategories = $this->getPath($product, 'Collections.Categories', []);
        
        if (is_array($katanaCategories)) {
            foreach ($katanaCategories as $category) {
                $categoryName = is_array($category) ? ($category['Name'] ?? null) : (string)$category;
                if ($categoryName) {
                    $categories[] = [
                        'name' => (string)$categoryName
                    ];
                }
            }
        }
        
        return $categories;
    }
    
    /**
     * Map attributes
     */
    private function mapAttributes(Integration $integration, array $product): array
    {
        $attributes = [];
        
        // Add GTIN if available
        $gtin = $this->getPath($product, 'TextFieldsModel.Gtin');
        if ($gtin) {
            $attributes[] = [
                'name' => 'GTIN',
                'visible' => true,
                'variation' => false,
                'options' => [(string)$gtin]
            ];
        }
        
        // Add specifications from KatanaPIM
        $specs = $this->getPath($product, 'Collections.Specs', []);
        if (is_array($specs)) {
            foreach ($specs as $spec) {
                $name = $spec['Name'] ?? null;
                $optionName = $spec['OptionName'] ?? null;
                if ($name && $optionName) {
                    $attributes[] = [
                        'name' => (string)$name,
                        'visible' => true,
                        'variation' => false,
                        'options' => [(string)$optionName]
                    ];
                }
            }
        }
        
        return $attributes;
    }
    
    /**
     * Map meta data
     */
    private function mapMetaData(Integration $integration, array $product): array
    {
        $metaData = [];
        
        // Add KatanaPIM ID
        if (isset($product['Id'])) {
            $metaData[] = [
                'key' => 'katana_id',
                'value' => (string)$product['Id']
            ];
        }
        
        // Add external key if available
        if (isset($product['ExternalKey'])) {
            $metaData[] = [
                'key' => 'katana_external_key',
                'value' => (string)$product['ExternalKey']
            ];
        }
        
        // Add GTIN as meta
        $gtin = $this->getPath($product, 'TextFieldsModel.Gtin');
        if ($gtin) {
            $metaData[] = [
                'key' => 'gtin',
                'value' => (string)$gtin
            ];
        }
        
        return $metaData;
    }
    
    /**
     * Find existing product in WooCommerce
     */
    private function findWooProduct(Integration $integration, array $product): ?array
    {
        $sku = $this->getPath($product, 'TextFieldsModel.Sku');
        
        if (!$sku) {
            return null;
        }
        
        $url = rtrim($integration->webshop_url, '/') . '/wp-json/wc/v3/products';
        
        // Configure HTTP client with SSL options for development
        $httpClient = Http::withBasicAuth(
            $integration->woo_commerce_api_key,
            $integration->woo_commerce_api_secret
        );
        
        // Disable SSL verification for development environments
        if (app()->environment('local', 'development')) {
            $httpClient = $httpClient->withoutVerifying();
        }
        
        $response = $httpClient->get($url, [
            'sku' => $sku
        ]);
        
        if ($response->successful()) {
            $products = $response->json();
            return !empty($products) ? $products[0] : null;
        }
        
        return null;
    }
    
    /**
     * Create new product in WooCommerce
     */
    private function createWooProduct(Integration $integration, array $productData)
    {
        $url = rtrim($integration->webshop_url, '/') . '/wp-json/wc/v3/products';
        
        // Configure HTTP client with SSL options for development
        $httpClient = Http::withBasicAuth(
            $integration->woo_commerce_api_key,
            $integration->woo_commerce_api_secret
        );
        
        // Disable SSL verification for development environments
        if (app()->environment('local', 'development')) {
            $httpClient = $httpClient->withoutVerifying();
        }
        
        $response = $httpClient->post($url, $productData);
        
        if (!$response->successful()) {
            throw new Exception("Failed to create product in WooCommerce: " . $response->body());
        }
        
        return $response->json();
    }
    
    /**
     * Update existing product in WooCommerce
     */
    private function updateWooProduct(Integration $integration, int $productId, array $productData)
    {
        $url = rtrim($integration->webshop_url, '/') . "/wp-json/wc/v3/products/{$productId}";
        
        // Configure HTTP client with SSL options for development
        $httpClient = Http::withBasicAuth(
            $integration->woo_commerce_api_key,
            $integration->woo_commerce_api_secret
        );
        
        // Disable SSL verification for development environments
        if (app()->environment('local', 'development')) {
            $httpClient = $httpClient->withoutVerifying();
        }
        
        $response = $httpClient->put($url, $productData);
        
        if (!$response->successful()) {
            throw new Exception("Failed to update product in WooCommerce: " . $response->body());
        }
        
        return $response->json();
    }
}
