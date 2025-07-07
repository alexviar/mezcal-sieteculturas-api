<?php

use App\Models\User;
use Tests\TestCase;

describe('dashboard index', function(){
    $endpoint = '/api/dashboard';
    
    test('An authenticated user is able to access the dashboard index.', function () use($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();
        $response = $this->actingAs($login)->getJson($endpoint);
    
        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user is denied access to the dashboard index.', function () use($endpoint) {
        /** @var TestCase $this */
        $response = $this->getJson($endpoint);
    
        $response->assertUnauthorized();
    });
});

