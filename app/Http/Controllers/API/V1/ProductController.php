<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index() {
               
    }

    public function store(Request $request) {
        try {
            $data = Product::validate($request);
        } catch (\Throwable $th) {
            return response()->api(null, "Invalid data", $th->errors());
        }

        $product = Product::create($data);

        return response()->api([
            'message' => "Product created",
            'product' =>  $product->filtered()
        ]);
    }

    public function show(Product $product) {
        return response()->api($product->filtered());
    }

    public function update(Product $product, Request $request) {
        try {
            $data = Product::validate($request, $product->id);
        } catch (\Throwable $th) {
            return response()->api(null, "Invalid data", $th->errors());
        }

        $product->update($data);

        return response()->api([
            'message' => "Product updated",
            'product' =>  $product->filtered()
        ]);
    }

    public function destroy(Product $product) {
        $product->delete();

        return response()->api([
            'message' => "Product deleted"
        ]);
    }
}
