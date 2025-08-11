<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Integration>
 */
class IntegrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'integration_name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'selected_store' => $this->faker->randomElement(['store1', 'store2', 'store3']),
            'unique_identifier' => $this->faker->uuid(),
            'identification_type' => $this->faker->randomElement(['SKU', 'GTIN', 'ExternalKey']),
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
            'condition_value' => $this->faker->word(),
            'meta_title' => $this->faker->sentence(),
            'meta_description' => $this->faker->paragraph(),
            'keywords' => $this->faker->words(5, true),
            'katana_pim_url' => $this->faker->url(),
            'katana_pim_api_key' => $this->faker->regexify('[A-Za-z0-9]{32}'),
            'webshop_url' => $this->faker->url(),
            'woo_commerce_api_key' => $this->faker->regexify('[A-Za-z0-9]{24}'),
            'woo_commerce_api_secret' => $this->faker->regexify('[A-Za-z0-9]{32}'),
            'store_mapping' => $this->faker->randomElement(['store1', 'store2', 'store3']),
            'field_name' => $this->faker->word(),
            'field_gtin' => $this->faker->word(),
            'field_short_description' => $this->faker->word(),
            'field_long_description' => $this->faker->word(),
            'field_tax_category' => $this->faker->word(),
            'select_value_1' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_2' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_3' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_4' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_5' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_6' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_7' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_8' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_9' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_10' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_11' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_12' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_13' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            'select_value_14' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
        ];
    }
}
