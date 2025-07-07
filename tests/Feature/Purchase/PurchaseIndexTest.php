<?php

use App\Models\Purchase;
use App\Models\User;
use Tests\TestCase;

describe('purchase index', function () {
    $endpoint = '/api/purchases';

    test('An authenticated user is able to access the purchases index.', function () use ($endpoint) {
        /** @var User $login */
        $login = User::factory()->create();
        $response = $this->actingAs($login)->getJson($endpoint);

        expect($response->getStatusCode())->not->toBe(401);
    });

    test('An unauthenticated user is denied access to the purchases index.', function () use ($endpoint) {
        /** @var TestCase $this */
        $response = $this->getJson($endpoint);

        $response->assertUnauthorized();
    });

    test('response structure', function () use ($endpoint) {
        /** @var TestCase $this */
        /** @var User $login */
        $login = User::factory()->create();
        Purchase::factory(5)->create();

        $response = $this->actingAs($login)->getJson($endpoint);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'product' => [
                        'name'
                    ]
                ]
            ]
        ]);
    });

    test('filter only shipped', function () use ($endpoint) {
        /** @var TestCase $this */
        /** @var User $login */
        $login = User::factory()->create();
        Purchase::factory()->pending()->create();
        $shipped = Purchase::factory()->shipped()->create();
        $response = $this->actingAs($login)->getJson($endpoint);
        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        $response = $this->actingAs($login)->getJson($endpoint . '?' . http_build_query(['filter' => ['status' => '2']]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $data = $response->json('data');
        $containsValue = collect($data)->contains('id', $shipped->id);
        $this->assertTrue($containsValue);
    });

    test('filter only pending', function () use ($endpoint) {
        /** @var TestCase $this */
        /** @var User $login */
        $login = User::factory()->create();
        $pending = Purchase::factory()->pending()->create();
        $pending2 = Purchase::factory(['shipped' => null])->create();
        Purchase::factory()->shipped()->create();
        $response = $this->actingAs($login)->getJson($endpoint);
        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        $response = $this->actingAs($login)->getJson($endpoint . '?' . http_build_query(['filter' => ['status' => '1']]));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $data = $response->json('data');
        $containsValue = collect($data)->contains('id', $pending->id);
        $this->assertTrue($containsValue);
        $containsValue = collect($data)->contains('id', $pending2->id);
        $this->assertTrue($containsValue);
    });
});
