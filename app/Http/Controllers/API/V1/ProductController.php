<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index(Request $request) {
        $products = Product::where(function($query) use ($request) {
            if($request->has('name'))
                $query->where('name', 'like', "%{$request->name}%");
        })->paginate(10);

        $meta = [
            'page' => [
                'current' => (int)$request->page,
                'per_page' => 10,
                'form' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
                'last-page' => $products->lastPage()
            ]
        ];

        $links = [
            'first' => $products->url(1),
            'last' => $products->url($products->lastPage()),
            'prev' => $products->previousPageUrl(),
            'next' => $products->nextPageUrl()
        ];

        $data = $products->map(function($product) use ($request) {
            if($request->has('show_not_available') && $request->show_not_available)
                return $product->filtered();

            if($product->available_quantities() > 0)
                return $product->filtered();
        });

        return response()->api([
            'meta' => $meta,
            'links' => $links,
            'products' => $data
        ]);
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
