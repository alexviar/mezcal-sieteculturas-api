<?php

use App\Models\User;
use Tests\TestCase;

describe('purchase receipt download', function(){
    $endpoint = fn($id) => '/api/purchases/'.$id.'/download';
    
    test('An authenticated user can download purchase receipt.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();

        $response = $this->actingAs($login)->getJson($endpoint(100));
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user cannot download purchase receipt.', function () use($endpoint) {
        /** @var TestCase $this */

        $response = $this->getJson($endpoint(100));
    
        $response->assertUnauthorized();
    });
});

