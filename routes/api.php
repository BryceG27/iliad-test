<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/user')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function() {
        Route::get('/', [AuthController::class, 'get']);
        Route::patch('/', [AuthController::class, 'update']);
        Route::delete('/', [AuthController::class, 'destroy']);
    });

    Route::post('/', [AuthController::class, 'store']);
});

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::resource('orders', OrderController::class)->except(['create', 'edit']);
        Route::put('/orders/{order}/confirm', [OrderController::class, 'confirm']);

        Route::resource('products', ProductController::class)->except(['create', 'edit']);
    });
});

Route::get('/testing', function() {
    $product = \App\Models\Product::find(1);
    $product->available_quantities();
});