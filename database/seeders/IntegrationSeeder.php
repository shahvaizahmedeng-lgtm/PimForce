<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Database\Seeder;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample user if none exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create sample integrations
        Integration::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
    }
}
