<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            
            // User and metadata
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->string('status')->default('active');
            
            // Step 1: Template
            $table->string('integration_name')->nullable();
            $table->text('description')->nullable();
            $table->string('selected_store')->nullable();
            $table->string('unique_identifier')->nullable();
            $table->string('identification_type')->default('SKU');
            $table->string('condition')->nullable();
            $table->string('condition_value')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            
            // Step 2: API keys
            $table->string('katana_pim_url')->nullable();
            $table->string('katana_pim_api_key')->nullable();
            $table->string('webshop_url')->nullable();
            $table->string('woo_commerce_api_key')->nullable();
            $table->string('woo_commerce_api_secret')->nullable();
            
            // Step 3: Mapping
            $table->string('store_mapping')->nullable();
            
            // Step 4: Internal fields mapping
            $table->string('field_name')->nullable();
            $table->string('field_gtin')->nullable();
            $table->string('field_short_description')->nullable();
            $table->string('field_long_description')->nullable();
            $table->string('field_tax_category')->nullable();
            
            // Select values for mapping
            $table->string('select_value_1')->nullable();
            $table->string('select_value_2')->nullable();
            $table->string('select_value_3')->nullable();
            $table->string('select_value_4')->nullable();
            $table->string('select_value_5')->nullable();
            $table->string('select_value_6')->nullable();
            $table->string('select_value_7')->nullable();
            $table->string('select_value_8')->nullable();
            $table->string('select_value_9')->nullable();
            $table->string('select_value_10')->nullable();
            $table->string('select_value_11')->nullable();
            $table->string('select_value_12')->nullable();
            $table->string('select_value_13')->nullable();
            $table->string('select_value_14')->nullable();
            
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
