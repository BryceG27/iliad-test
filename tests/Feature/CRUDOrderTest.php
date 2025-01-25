<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;

uses (WithFaker::class);

it('can create an order', function () {
    $product = Product::factory()->create();

    $data = [
        'name' => $this->faker->name,
        'description' => $this->faker->text,
        'date' => date('Y-m-d'),
        'products' => [
            [
                'id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 10)
            ]
        ]
    ];

    login()->post('/api/v1/orders', $data)
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'error_message' => null,
            'errors' => null,
            'data' => [
                'message' => 'Order created',
                'order' => [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'date' => $data['date'],
                    'status' => "Pending",
                    'costs' => $product->price * $data['products'][0]['quantity'],
                    'type' => "To Customer",
                    'products' => [
                        [
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $data['products'][0]['quantity']
                        ]
                    ]
                ]
            ]
        ]);
});

it('can update an order', function () {
    $product = Product::factory()->create();
    $product2 = Product::factory()->create();

    $data = [
        'name' => $this->faker->name,
        'description' => $this->faker->text,
        'date' => date('Y-m-d'),
        'products' => [
            [
                'id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 5)
            ]
        ]
    ];

    login()->post('/api/v1/orders', $data)
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'error_message' => null,
            'errors' => null,
            'data' => [
                'message' => 'Order created',
                'order' => [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'date' => $data['date'],
                    'status' => "Pending",
                    'costs' => $product->price * $data['products'][0]['quantity'],
                    'type' => "To Customer",
                    'products' => [
                        [
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $data['products'][0]['quantity']
                        ]
                    ]
                ]
            ]
        ]);

    $order = Order::latest()->first();

    $response = login()->patch("/api/v1/orders/{$order->id}", [
        'name' => 'Order 1 Updated',
        'description' => 'Order 1 Description Updated',
        'date' => date('Y-m-d'),
        'products' => [
            [
                'id' => $product->id,
                'quantity' => 2
            ],
            [
                'id' => $product2->id,
                'quantity' => 4
            ]
        ]
    ])->assertStatus(200);
        
    $response->assertJson([
        'success' => true,
        'error_message' => null,
        'errors' => null,
        'data' => [
            'message' => 'Order updated',
            'order' => [
                'name' => 'Order 1 Updated',
                'description' => 'Order 1 Description Updated',
                'date' => date('Y-m-d'),
                'status' => "Pending",
                'costs' => ($product->price * 2) + ($product2->price * 4),
                'type' => "To Customer",
                'products' => [

                ]
            ]
        ]
    ]);   
});

it('can delete an order', function() {
    $product = Product::factory()->create();

    $data = [
        'name' => $this->faker->name,
        'description' => $this->faker->text,
        'date' => date('Y-m-d'),
        'products' => [
            [
                'id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 10)
            ]
        ]
    ];

    login()->post('/api/v1/orders', $data);

    $order = Order::latest()->first();

    login()->delete("/api/v1/orders/{$order->id}")
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'error_message' => null,
            'errors' => null,
            'data' => null
        ]);
});

test("there are missing parameters for the store of the order", function() {
    $response = login()->post('/api/v1/orders', []);

    $response->assertJson([
            'success' => false,
            'errors' => [
                'products' => ['The products field is required.']
            ],
            'data' => null
        ]);
});

test("product inserted in the order not found", function() {
    $response = login()->post('/api/v1/orders', [
        'name' => $this->faker->name,
        'description' => $this->faker->text,
        'date' => date('Y-m-d'),
        'products' => [
            [
                'id' => 1,
                'quantity' => $this->faker->numberBetween(1, 10)
            ]
        ]
    ]);

    $response->assertJson([
        'success' => false,
        'errors' => [
            'products.0.id' => ['The selected products.0.id is invalid.']
        ],
        'data' => null
    ]);
});