<?php

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

describe('product update', function(){
    $endpoint = fn($id) => '/api/products/'.$id;
    
    test('An authenticated user can update products.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();

        $response = $this->actingAs($login)->putJson($endpoint(100));
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user cannot update products.', function () use($endpoint) {
        /** @var TestCase $this */

        $response = $this->putJson($endpoint(100));
    
        $response->assertUnauthorized();
    });
});

