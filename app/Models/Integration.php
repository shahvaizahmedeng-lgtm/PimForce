<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'step',
        'data',
        'api_data',
        'store_data',
        'unique_identifier',
        'fields_mapping_data',
        'specifications',
        'seo_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'data' => 'array',
        'api_data' => 'array',
        'store_data' => 'array',
        'unique_identifier' => 'array',
        'fields_mapping_data' => 'array',
        'specifications' => 'array',
        'seo_data' => 'array',
    ];

    /**
     * Boot the model and set default values for JSON fields
     */
    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($model) {
            // Ensure JSON fields are always arrays, not null
            $jsonFields = ['data', 'api_data', 'store_data', 'unique_identifier', 'fields_mapping_data', 'specifications', 'seo_data'];
            
            foreach ($jsonFields as $field) {
                if ($model->$field === null) {
                    $model->$field = [];
                }
            }
        });
    }

    /**
     * Get a safe string value from a JSON field
     */
    private function getSafeString($field, $key = null, $default = 'Not configured')
    {
        $value = $this->$field;
        
        if ($value === null || !is_array($value)) {
            return $default;
        }
        
        if ($key === null) {
            return is_string($value) ? $value : $default;
        }
        
        $nestedValue = $value[$key] ?? null;
        return is_string($nestedValue) ? $nestedValue : $default;
    }

    /**
     * Get a safe array value from a JSON field
     */
    private function getSafeArray($field, $default = [])
    {
        $value = $this->$field;
        return is_array($value) ? $value : $default;
    }

    /**
     * Get the user that owns the integration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get integration name from integrationDetails
     */
    public function getIntegrationNameAttribute()
    {
        return $this->data['integrationName'] ?? null;
    }

    /**
     * Get integration description from integrationDetails
     */
    public function getDescriptionAttribute()
    {
        return $this->data['integrationDesc'] ?? null;
    }

    /**
     * Get KatanaPIM URL from apiDetails
     */
    public function getKatanaPimUrlAttribute()
    {
        return $this->getSafeString('api_data', 'katanaPimUrl', null);
    }

    /**
     * Get KatanaPIM API Key from apiDetails
     */
    public function getKatanaPimApiKeyAttribute()
    {
        return $this->getSafeString('api_data', 'katanaPimApiKey', null);
    }

    /**
     * Get Webshop URL from apiDetails
     */
    public function getWebshopUrlAttribute()
    {
        return $this->getSafeString('api_data', 'webshopUrl', null);
    }

    /**
     * Get WooCommerce API Key from apiDetails
     */
    public function getWooCommerceApiKeyAttribute()
    {
        return $this->getSafeString('api_data', 'wooCommerceApiKey', null);
    }

    /**
     * Get WooCommerce API Secret from apiDetails
     */
    public function getWooCommerceApiSecretAttribute()
    {
        return $this->getSafeString('api_data', 'wooCommerceApiSecret', null);
    }

    /**
     * Get Unique Identifier from uniqueIdentifier
     */
    public function getUniqueIdentifierValueAttribute()
    {
        return $this->unique_identifier['identifier'] ?? null;
    }

    /**
     * Get Identification Type from uniqueIdentifier
     */
    public function getIdentificationTypeAttribute()
    {
        return $this->unique_identifier['identificationType'] ?? null;
    }

    /**
     * Get Product Condition from productCondition
     */
    public function getProductConditionValueAttribute()
    {
        return $this->data['productCondition'] ?? null;
    }

    /**
     * Get Product Condition Value from productCondition
     */
    public function getProductConditionValueStringAttribute()
    {
        return $this->data['conditionValue'] ?? null;
    }

    /**
     * Get Store Name from store_details
     */
    public function getStoreNameAttribute()
    {
        return $this->store_data['store_name'] ?? null;
    }

    /**
     * Get Store Type from store_details
     */
    public function getStoreTypeAttribute()
    {
        return $this->store_data['store_type'] ?? null;
    }
}
