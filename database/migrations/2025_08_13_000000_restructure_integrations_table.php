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
        Schema::table('integrations', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
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
            ]);
        });

        Schema::table('integrations', function (Blueprint $table) {
            // Add new JSON columns
            $table->json('integrationDetails')->nullable()->after('status');
            $table->json('apiDetails')->nullable()->after('integrationDetails');
            $table->json('uniqueIdentifier')->nullable()->after('apiDetails');
            $table->json('internalFields')->nullable()->after('uniqueIdentifier');
            $table->json('productCondition')->nullable()->after('internalFields');
            $table->json('seo')->nullable()->after('productCondition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integrations', function (Blueprint $table) {
            // Drop new JSON columns
            $table->dropColumn([
                'integrationDetails',
                'apiDetails',
                'uniqueIdentifier',
                'internalFields',
                'productCondition',
                'seo'
            ]);
        });

        Schema::table('integrations', function (Blueprint $table) {
            // Re-add old columns
            $table->string('integration_name')->nullable()->after('status');
            $table->text('description')->nullable()->after('integration_name');
            $table->string('selected_store')->nullable()->after('description');
            $table->string('unique_identifier')->nullable()->after('selected_store');
            $table->string('identification_type')->default('SKU')->after('unique_identifier');
            $table->string('condition')->nullable()->after('identification_type');
            $table->string('condition_value')->nullable()->after('condition');
            $table->string('meta_title')->nullable()->after('condition_value');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('keywords')->nullable()->after('meta_description');
            $table->string('katana_pim_url')->nullable()->after('keywords');
            $table->string('katana_pim_api_key')->nullable()->after('katana_pim_url');
            $table->string('webshop_url')->nullable()->after('katana_pim_api_key');
            $table->string('woo_commerce_api_key')->nullable()->after('webshop_url');
            $table->string('woo_commerce_api_secret')->nullable()->after('woo_commerce_api_key');
            $table->string('store_mapping')->nullable()->after('woo_commerce_api_secret');
            $table->string('field_name')->nullable()->after('store_mapping');
            $table->string('field_gtin')->nullable()->after('field_name');
            $table->string('field_short_description')->nullable()->after('field_gtin');
            $table->string('field_long_description')->nullable()->after('field_short_description');
            $table->string('field_tax_category')->nullable()->after('field_long_description');
            $table->string('select_value_1')->nullable()->after('field_tax_category');
            $table->string('select_value_2')->nullable()->after('select_value_1');
            $table->string('select_value_3')->nullable()->after('select_value_2');
            $table->string('select_value_4')->nullable()->after('select_value_3');
            $table->string('select_value_5')->nullable()->after('select_value_4');
            $table->string('select_value_6')->nullable()->after('select_value_5');
            $table->string('select_value_7')->nullable()->after('select_value_6');
            $table->string('select_value_8')->nullable()->after('select_value_7');
            $table->string('select_value_9')->nullable()->after('select_value_8');
            $table->string('select_value_10')->nullable()->after('select_value_9');
            $table->string('select_value_11')->nullable()->after('select_value_10');
            $table->string('select_value_12')->nullable()->after('select_value_11');
            $table->string('select_value_13')->nullable()->after('select_value_12');
            $table->string('select_value_14')->nullable()->after('select_value_13');
        });
    }
};
