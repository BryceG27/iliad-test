<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends \App\Http\Controllers\Controller
{
    public function index(Request $request) {
        $orders = Order::where('user_id', Auth::user()->id)->where(function($query) use ($request) {

            // If the request has the show_deleted key and the value is true, then the query will include the deleted orders of the user
            if($request->has('show_deleted') && $request->show_deleted)
                $query->withTrashed();

            if($request->has('date'))
                $query->whereDate('date', $request->date);

            if($request->has('name'))
                $query->where('name', 'like', "%{$request->name}%");
            
            if($request->has('description'))
                $query->where('description', 'like', "%{$request->description}%");

        })->paginate(10);

        $meta = [
            'page' => [
                'current' => (int)$request->page,
                'per_page' => 10,
                'form' => $orders->firstItem(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
                'last-page' => $orders->lastPage()
            ]
        ];

        $links = [
            'first' => $orders->url(1),
            'last' => $orders->url($orders->lastPage()),
            'prev' => $orders->previousPageUrl(),
            'next' => $orders->nextPageUrl()
        ];

        $data = $orders->map(function($order) {
            return $order->filtered();
        });

        return response()->api([
            'meta' => $meta,
            'links' => $links,
            'orders' => $data
        ]);
    }

    /**
     * Show the order with details
     * 
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse with the order details and the products in the order
     */
    public function show(Order $order) {
        return response()->api(
            $order->filtered()
        );
    }

    /**
     * Store a new order
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $user = Auth::user();
        $data = Order::validate($request);
        $data['user_id'] = $user->id;

        if($data['date'] == null) // If the date is not provided, the current date will be used
            $data['date'] = date('Y-m-d');

        $order = Order::create($data);

        foreach ($data['products'] as $product_data) {
            $product = Product::find($product_data['id']);
            if($product->available_quantities() < $product_data['quantity'])
                return response()->api(null, "The product \"{$product->name}\" does not have enough quantities", [200]);

            $order->products()->attach($product->id, [
                'quantity' => $product_data['quantity']
            ]);
        }

        return response()->api([
            'message' => "Order created",
            'order' =>  $order->filtered()
        ]);
    }


    /**
     * Delete the order
     * If the order is already confirmed, the quantities of the products in the order will be restored
     * 
     * @param Order $order
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $order) {
        if($order->status == 1) {
            $order->products->map(function($product) use ($order) {
                if($order->type == 0) 
                    $new_quantity = $product->quantity + $product->pivot->quantity;
                else if($order->type == 1)
                    $new_quantity = $product->quantity - $product->pivot->quantity;
    
                $product->update([
                    'quantity' => $new_quantity
                ]);
            });
        }

        $order->update([
            'status' => 2
        ]);
        $order->delete();

        return response()->api([
            'message' => "Order deleted"
        ]);
    }

    /**
     * Update the order
     * 
     * @param Order $order
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Order $order, Request $request) {
        if($order->status == 1)
            return response()->api(null, "The order is already confirmed. Update not available", [200]);

        $data = Order::validate($request);

        $order->update($data);

        $formatted_products = [];
        $response_msg = "Order updated";

        foreach ($data['products'] as $product_data) {
            $product = Product::find($product_data['id']);

            if($product->available_quantities() < $product_data['quantity']) {
                $response_msg .= "\nThe product --- {$product->name} --- does not have the request quantities in stock. Quantity ordered: {$product->available_quantities()}.";

                $formatted_products[$product->id] = [ 'quantity' => $product->available_quantities() ];
            } else {
                $formatted_products[$product->id] = [ 'quantity' => $product_data['quantity'] ];
            }
        }

        $order->products()->sync($formatted_products);

        return response()->api([
            'message' => $response_msg,
            'order' => $order->filtered()
        ]);
    }

    /**
     * Confirm the order
     * If the order type is 0, the quantities of the products in the order will be deducted from the product quantities
     * If the order type is 1, the quantities of the products in the order will be added to the product quantities
     * 
     * @param Order $order
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Order $order) {
        $order->update([
            'status' => 1
        ]);

        $order->products->map(function($product) use ($order) {
            if($order->type == 0) 
                $new_quantity = $product->quantity - $product->pivot->quantity;
            else if($order->type == 1)
                $new_quantity = $product->quantity + $product->pivot->quantity;

            $product->update([
                'quantity' => $new_quantity
            ]);
        });
        

        return response()->api([
            'message' => "Order confirmed",
        ]);
    }
}
