<?php

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;

uses (WithFaker::class);

test('users can authenticate', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});

test('users can authenticate using the API route', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    // $response->assertNoContent();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can not authenticate with invalid password with API', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(200);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/api/logout');

    $response->assertStatus(200);
});


test('user is not logged', function() {
    $this->post('/api/v1/products', [
        'name' => $this->faker->name,
        'price' => $this->faker->randomFloat(3),
        'quantity' => $this->faker->numberBetween(0, 100),
    ])->assertJson([
        'success' => false,
        'error_message' => 'Unauthenticated.',
        'errors' => ['Unauthenticated.', 401],
        'data' => null,
    ]);
});