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
            'integrationDetails' => [
                'integrationName' => $this->faker->words(3, true),
                'integrationDesc' => $this->faker->sentence(),
            ],
            'apiDetails' => [
                'katanaPimUrl' => $this->faker->url(),
                'katanaPimApiKey' => $this->faker->regexify('[A-Za-z0-9]{32}'),
                'webshopUrl' => $this->faker->url(),
                'wooCommerceApiKey' => $this->faker->regexify('[A-Za-z0-9]{24}'),
                'wooCommerceApiSecret' => $this->faker->regexify('[A-Za-z0-9]{32}'),
            ],
            'store_details' => [
                'store_name' => $this->faker->randomElement(['Store 1', 'Store 2', 'Store 3']),
                'store_type' => $this->faker->randomElement(['main', 'secondary', 'custom']),
                'store_url' => $this->faker->url(),
                'store_description' => $this->faker->sentence(),
            ],
            'uniqueIdentifier' => [
                'identifier' => $this->faker->uuid(),
                'identificationType' => $this->faker->randomElement(['SKU', 'GTIN', 'ExternalKey']),
            ],
            'internalFields' => [
                'fieldName' => $this->faker->word(),
                'fieldGtin' => $this->faker->word(),
                'fieldShortDescription' => $this->faker->word(),
                'fieldLongDescription' => $this->faker->word(),
                'fieldTaxCategory' => $this->faker->word(),
                'selectValue1' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue2' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue3' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue4' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue5' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue6' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue7' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue8' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue9' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue10' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue11' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue12' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue13' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
                'selectValue14' => $this->faker->randomElement(['SKU-1', 'SKU-2', 'SKU-3']),
            ],
            'productCondition' => [
                'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
                'conditionValue' => $this->faker->word(),
            ],
            'seo' => [
                'metaTitle' => $this->faker->sentence(),
                'metaDescription' => $this->faker->paragraph(),
                'keywords' => $this->faker->words(5, true),
            ],
        ];
    }
}
