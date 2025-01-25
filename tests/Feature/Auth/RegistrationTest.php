<?php

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});

test("new users can register with api route", function() {
    $response = $this->post('/api/register', [
        'name' => 'Test User 2',
        'email' => 'test2@example.com',
        'password' => 'supersecurepassword',
        'password_confirmation' => 'supersecurepassword',
    ]);

    $user = \App\Models\User::latest()->first();

    $response->assertStatus(200);

    expect($user->name)->not->toBeEmpty()->toBe('Test User 2');
    expect($user->email)->not->toBeEmpty()->toBe('test2@example.com');
});