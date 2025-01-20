<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        throw new Exception("Error Processing Request", 1);
        

        return response()->api([
            'message' => 'Welcome to the API',
        ]);
    });
});