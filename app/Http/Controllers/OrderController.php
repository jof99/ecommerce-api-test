<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();
    
        // get the user's cart
        $cartItems = $user->cartItems()->with('product')->get();
    
        // check if there are products in the cart
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products in the cart'
            ], 400);
        }
    
        // create a new order for the user
        $order = new Order([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
        $order->save();
    
        // add each cart item as an order item
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderProduct([
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
                'order_id' => $order->id
            ]);
            $orderItem->save();
    
            // remove the cart item from the user's cart
            $cartItem->delete();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully'
        ], 200);
    }
    

    
    

    
}
