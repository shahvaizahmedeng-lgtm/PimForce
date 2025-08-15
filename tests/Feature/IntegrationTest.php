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
        'integrationDetails' => [
            'integrationName' => 'Test Integration',
            'integrationDesc' => 'Test integration description',
        ],
        'apiDetails' => [
            'katanaPimUrl' => 'https://katana.example.com',
            'katanaPimApiKey' => 'test_api_key_123',
            'webshopUrl' => 'https://shop.example.com',
            'wooCommerceApiKey' => 'woo_key_123',
            'wooCommerceApiSecret' => 'woo_secret_123',
        ],
        'store_details' => [
            'store_name' => 'Store 1',
            'store_type' => 'main',
        ],
        'uniqueIdentifier' => [
            'identifier' => 'SKU',
            'identificationType' => 'SKU-1',
        ],
        'internalFields' => [],
        'productCondition' => [],
        'seo' => [],
    ];
    
    $integration = Integration::create($integrationData);
    
    $this->assertDatabaseHas('integrations', [
        'id' => $integration->id,
        'user_id' => $user->id,
        'status' => 'active',
    ]);
    
    $this->assertEquals($user->id, $integration->user->id);
    $this->assertEquals('Test Integration', $integration->integrationName);
    $this->assertEquals('https://katana.example.com', $integration->katanaPimUrl);
});

test('integration belongs to user', function () {
    $user = User::factory()->create();
    $integration = Integration::factory()->create(['user_id' => $user->id]);
    
    $this->assertTrue($integration->user->is($user));
    $this->assertTrue($user->integrations->contains($integration));
});
