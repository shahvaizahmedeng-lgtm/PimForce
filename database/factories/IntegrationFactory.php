<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntegrationStepperFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'step' => $this->faker->numberBetween(1, 10),
            'data' => [
                'example_key' => $this->faker->word(),
            ],
            'store_data' => [
                'store_name' => $this->faker->company(),
                'store_url' => $this->faker->url(),
            ],
            'api_data' => [
                'api_url' => $this->faker->url(),
                'api_key' => $this->faker->sha256(),
            ],
            'fields_mapping_data' => [
                'field1' => $this->faker->word(),
                'field2' => $this->faker->word(),
            ],
            'seo_data' => [
                'meta_title' => $this->faker->sentence(),
                'meta_description' => $this->faker->paragraph(),
            ],
            'specifications' => $this->faker->words(5), // array of strings
            'unique_identifier' => [
                'identifier' => $this->faker->uuid(),
            ],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
