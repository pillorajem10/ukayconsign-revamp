<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\StoreInventory;
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
    
        // Check if the status is "Delivered"
        if ($order->order_status === 'Delivered') {
            // Decode the JSON string into an array
            $productsOrdered = json_decode($order->products_ordered, true); // Set second parameter to true for associative array
    
            // Check if decoding was successful and it's an array
            if (is_array($productsOrdered)) {
                foreach ($productsOrdered as $product) {
                    // Check if SKU already exists in StoreInventory for the specific user
                    $inventoryItem = StoreInventory::where('SKU', $product['product_sku'])
                        ->where('store_id', $product['store_id']) // Assuming store_id is used to identify the user
                        ->first();
                    
                    if ($inventoryItem) {
                        // If it exists, update the Stocks
                        $inventoryItem->Stocks += $product['quantity'];
                        $inventoryItem->save();
                    } else {
                        // If it doesn't exist, create a new record
                        StoreInventory::create([
                            'SKU' => $product['product_sku'],
                            'ProductID' => $product['product_id'],
                            'Stocks' => $product['quantity'],
                            'Consign' => $product['product_consign'],
                            'SPR' => $product['product_srp'],
                            'store_id' => $product['store_id'],
                        ]);
                    }
                }
            } else {
                // Handle the error if products_ordered is not valid JSON
                return redirect()->route('orders.index')->with('error', 'Invalid product data format!');
            }
        }
    
        // Save the order status after processing the products
        $order->save();
    
        return redirect()->route('orders.index')->with('success', 'Order status updated successfully!');
    }             
}
