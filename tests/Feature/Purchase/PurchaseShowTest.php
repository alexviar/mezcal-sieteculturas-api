<?php

use App\Models\User;
use Tests\TestCase;

describe('purchase show', function(){
    $endpoint = fn($id) => '/api/purchases/'.$id;
    
    test('An authenticated user can get purchase detail.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();

        $response = $this->actingAs($login)->getJson($endpoint(100));
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user cannot get purchase detail.', function () use($endpoint) {
        /** @var TestCase $this */

        $response = $this->getJson($endpoint(100));
    
        $response->assertUnauthorized();
    });
});

