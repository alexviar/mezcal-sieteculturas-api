<?php

use App\Models\User;
use Tests\TestCase;

describe('product create', function(){
    $endpoint = '/api/products';
    
    test('An authenticated user can create products.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();

        $response = $this->actingAs($login)->postJson($endpoint);
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user cannot create products.', function () use($endpoint) {
        /** @var TestCase $this */

        $response = $this->postJson($endpoint);
    
        $response->assertUnauthorized();
    });
});

