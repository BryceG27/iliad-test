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

        })->filtered()->paginate(10);

        return response()->api(
            $orders
        );
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
        
    }

    public function update(Order $order, Request $request) {
        
    }

    public function destroy(Order $order) {
        
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
