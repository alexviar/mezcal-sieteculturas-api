<?php

use App\Models\User;
use Tests\TestCase;

describe('purchase update', function(){
    $endpoint = fn($id) => '/api/purchases/'.$id;
    
    test('An authenticated user can update purchase.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();

        $response = $this->actingAs($login)->putJson($endpoint(100));
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user cannot update purchase.', function () use($endpoint) {
        /** @var TestCase $this */

        $response = $this->putJson($endpoint(100));
    
        $response->assertUnauthorized();
    });
});

