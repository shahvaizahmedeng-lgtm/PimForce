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
        'integration_name',
        'description',
        'selected_store',
        'unique_identifier',
        'identification_type',
        'condition',
        'condition_value',
        'meta_title',
        'meta_description',
        'keywords',
        'katana_pim_url',
        'katana_pim_api_key',
        'webshop_url',
        'woo_commerce_api_key',
        'woo_commerce_api_secret',
        'store_mapping',
        'field_name',
        'field_gtin',
        'field_short_description',
        'field_long_description',
        'field_tax_category',
        'select_value_1',
        'select_value_2',
        'select_value_3',
        'select_value_4',
        'select_value_5',
        'select_value_6',
        'select_value_7',
        'select_value_8',
        'select_value_9',
        'select_value_10',
        'select_value_11',
        'select_value_12',
        'select_value_13',
        'select_value_14',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the integration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
