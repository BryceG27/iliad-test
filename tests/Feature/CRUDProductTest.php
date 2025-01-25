<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;

uses (WithFaker::class);

// 
it('can create a product', function () {
    $data = [
        'name' => $this->faker->name,
        'price' => $this->faker->randomFloat(3),
        'quantity' => $this->faker->numberBetween(0, 100),
    ];

    login()->post('/api/v1/products', [
        'name' => $data['name'],
        'price' => $data['price'],
        'quantity' => $data['quantity'],
    ])->assertStatus(200)->assertJson([
        'success' => true,
        'error_message' => null,
        'errors' => null,
        'data' => [
            'message' => 'Product created',
            'product' => [
                'name' => $data['name'],
                'price' => $data['price'],
                'quantity' => $data['quantity'],
                'available_quantities' => $data['quantity'],
            ]
        ]
    ]);
});

it('can update a product', function () {
    $data = [
        'name' => $this->faker->name,
        'price' => $this->faker->randomFloat(3),
        'quantity' => $this->faker->numberBetween(0, 100),
    ];

    login()->post('/api/v1/products', [
        'name' => $data['name'],
        'price' => $data['price'],
        'quantity' => $data['quantity'],
    ]);

    $product = Product::latest()->first();

    login()->put("/api/v1/products/{$product->id}", [
        'name' => 'Product 1 Updated',
        'price' => 200,
        'quantity' => 20,
    ])
    ->assertStatus(200)
    ->assertJson([
        'success' => true,
        'error_message' => null,
        'errors' => null,
        'data' => [
            'message' => 'Product updated',
            'product' => [
                'name' => 'Product 1 Updated',
                'price' => 200,
                'quantity' => 20,
                'available_quantities' => 20,
            ]
        ]
    ]);
});

it('can delete a product', function() {
    $data = [
        'name' => $this->faker->name,
        'price' => $this->faker->randomFloat(3),
        'quantity' => $this->faker->numberBetween(0, 100),
    ];

    login()->post('/api/v1/products', [
        'name' => $data['name'],
        'price' => $data['price'],
        'quantity' => $data['quantity'],
    ]);

    $product = Product::latest()->first();

    login()->delete("/api/v1/products/{$product->id}")
    ->assertStatus(200)
    ->assertJson([
        'success' => true,
        'error_message' => null,
        'errors' => null,
        'data' => [
            'message' => 'Product deleted',
        ]
    ]);
});

test('there are missing parameters for the store of the product', function () {
    $response = login()->post('/api/v1/products', [
        'price' => $this->faker->randomFloat(3),
        'quantity' => $this->faker->numberBetween(0, 100),
    ]);

    $response->assertJsonValidationErrors('name');
});