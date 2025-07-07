<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

describe('issue access token', function(){
    $endpoint = '/api/auth/login';

    it('get access token', function () use($endpoint) {
        /** @var TestCase $this */
        $email = 'test@example.com';
        $password = '1234';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson($endpoint, [
            'email' => $email,
            'password' => $password
        ]);
    
        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    });
    
    it('invalid email', function () use($endpoint) {
        /** @var TestCase $this */
        $email = 'test@example.com';
        $password = '1234';
        $user = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson($endpoint, [
            'email' => $email,
            'password' => $password
        ]);
    
        $response->assertUnauthorized();
    });
    
    it('invalid password', function () use($endpoint) {
        /** @var TestCase $this */
        $email = 'test@example.com';
        $password = '1234';
        User::factory()->create([
            'email' => $email,
            'password' => '5678'
        ]);

        $response = $this->postJson($endpoint, [
            'email' => $email,
            'password' => $password
        ]);
    
        $response->assertUnauthorized();
    });
});

