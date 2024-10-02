<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // Display the checkout page
    public function index()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to proceed to checkout.');
        }
    
        // Retrieve the cart items for the authenticated user
        $carts = Cart::where('user_id', Auth::id())->get();
    
        // Get existing store information
        $store = Store::where('store_owner', Auth::id())->first();
    
        // Get the most recent order information
        $latestOrder = Order::where('user_id', Auth::id())->orderBy('createdAt', 'desc')->first();
    
        return view('pages.checkout', compact('carts', 'store', 'latestOrder'));
    }      

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string|max:255',
            'store_phone_number' => 'required|string|max:20',
            'store_email' => 'required|email|max:255',
        ]);
    
        // Retrieve the cart items for the authenticated user
        $carts = Cart::where('user_id', Auth::id())->get();
    
        if ($carts->isNotEmpty()) {
            $productsOrdered = $carts->map(function($cart) {
                return [
                    'cart_id' => $cart->id,
                    'bundle_name' => $cart->product->Bundle,
                    'category' => $cart->product->Category,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price,
                ];
            });
    
            // Create the order
            Order::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_id' => Auth::id(),
                'products_ordered' => json_encode($productsOrdered),
                'address' => $request->address,
                'store_name' => $request->store_name,
                'email' => Auth::user()->email,
                'total_price' => $carts->sum(function($cart) {
                    return $cart->price * $cart->quantity;
                }),
                'order_date' => now(),
                'order_status' => 'Processing',
                'createdAt' => now(),
            ]);
    
            // Check for existing store by store_name
            $existingStore = Store::where('store_name', $request->store_name)->first();
    
            if ($existingStore) {
                // Update the existing store information
                $existingStore->update([
                    'store_address' => $request->store_address,
                    'store_phone_number' => $request->store_phone_number,
                    'store_email' => $request->store_email,
                ]);
                Log::info('Updated store data:', $existingStore->toArray());
            } else {
                // Create new store information
                $storeData = [
                    'store_name' => $request->store_name,
                    'store_owner' => Auth::id(), // Set the store_owner to the logged-in user's ID
                    'store_address' => $request->store_address,
                    'store_phone_number' => $request->store_phone_number,
                    'store_email' => $request->store_email,
                    'store_total_earnings' => 0, // Default earnings
                    'store_status' => 'active', // Default status
                ];
                Store::create($storeData);
                Log::info('Created store data:', $storeData);
            }
    
            // Clear the cart for the user
            Cart::where('user_id', Auth::id())->delete();
    
            return redirect()->route('home')->with('success', 'Order placed successfully!');
        }
    
        return redirect()->back()->with('error', 'Your cart is empty.');
    }  
}
