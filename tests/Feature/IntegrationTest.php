<?php

use App\Models\User;
use App\Models\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can create integration', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user);
    
    $integrationData = [
        'user_id' => $user->id,
        'status' => 'active',
        'katana_pim_url' => 'https://katana.example.com',
        'katana_pim_api_key' => 'test_api_key_123',
        'webshop_url' => 'https://shop.example.com',
        'woo_commerce_api_key' => 'woo_key_123',
        'woo_commerce_api_secret' => 'woo_secret_123',
        'selected_store' => 'store1',
        'integration_name' => 'Test Integration',
        'description' => 'Test integration description',
    ];
    
    $integration = Integration::create($integrationData);
    
    $this->assertDatabaseHas('integrations', [
        'id' => $integration->id,
        'user_id' => $user->id,
        'katana_pim_url' => 'https://katana.example.com',
        'status' => 'active',
    ]);
    
    $this->assertEquals($user->id, $integration->user->id);
});

test('integration belongs to user', function () {
    $user = User::factory()->create();
    $integration = Integration::factory()->create(['user_id' => $user->id]);
    
    $this->assertTrue($integration->user->is($user));
    $this->assertTrue($user->integrations->contains($integration));
});
