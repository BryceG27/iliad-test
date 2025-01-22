<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends \App\Http\Controllers\Controller
{
    public function index(Request $request) {
        $orders = Order::where('user_id', Auth::user()->id)->where(function($query) use ($request) {

            // If the request has the show_deleted key and the value is true, then the query will include the deleted orders of the user
            if($request->has('show_deleted') && $request->show_deleted)
                $query->withTrashed();

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

        /* $formatted_response = Order::formatted($request, $orders);

        dd($formatted_response); */

        return response()->api([
            'meta' => $meta,
            'links' => $links,
            'data' => $data
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
            $order->filtered()->load('products')
        );
    }

    public function store(Request $request) {
        $user = Auth::user();
        $data = Order::validate($request);
        $data['user_id'] = $user->id;

        if($data['date'] == null)
            $data['date'] = date('Y-m-d');

        $order = Order::create($data);

        foreach ($data['products'] as $product) {
            $order->products()->attach($product['id'], [
                'quantity' => $product['quantity']
            ]);
        }

        return response()->api([
            'message' => "Order created",
            'order' =>  $order->filtered()
        ]);
    }

    public function update(Order $order, Request $request) {
        
    }

    public function destroy(Order $order) {
        $order->update([
            'status' => 2
        ]);
        $order->delete();

        return response()->api([
            'message' => "Order deleted"
        ]);
    }

    /**
     *  Change status of the order:
     * 0 - Pending      --- Order pending means the product available quantities will be anavailable
     * 1 - Completed    --- Order completed means the product available quantities will be reduced
     * 2 - Cancelled
     * 
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse with TRUE if the status was changed successfully, FALSE otherwise
    * */
    public function change_status(Order $order) {
        
    }
}
