<?php

use App\Models\User;
use Tests\TestCase;

describe('product show', function(){
    $endpoint = fn($id) => '/api/products/'.$id;
    
    test('An authenticated user can get product detail.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();

        $response = $this->actingAs($login)->getJson($endpoint(100));
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user cannot get product detail.', function () use($endpoint) {
        /** @var TestCase $this */

        $response = $this->getJson($endpoint(100));
    
        $response->assertUnauthorized();
    });
});

