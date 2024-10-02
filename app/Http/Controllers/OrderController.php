<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }
    
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->get(); // Fetch orders for the logged-in user
        return view('pages.orders', compact('orders')); // Pass the orders to the view
    }    

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string|in:Processing,Packed,Shipped,Delivered,Canceled',
        ]);
        
        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order status updated successfully!');
    }
}
