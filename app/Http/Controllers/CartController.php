<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;


class CartController extends Controller
{
    public function index()
    {
        // Get user ID based on authentication status
        $userId = Auth::check() ? Auth::id() : session()->getId(); // Use session ID if not logged in
    
        // Retrieve the cart items for the user
        $carts = Cart::where('user_id', $userId)->get();
    
        // Check if there are no cart items
        if ($carts->isEmpty()) {
            return redirect()->route('home')->with('error', 'There are no items in your cart.');
        }
    
        return view('pages.cartPage', compact('carts'));
    }     

    // ADD TO CART
    public function add(Request $request)
    {
        // Use session ID as user ID for unauthenticated users
        $userId = Auth::check() ? Auth::id() : session()->getId(); // Get session ID for non-logged users
    
        // Validate incoming request
        $request->validate([
            'products' => 'required|array',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price_type' => 'required|string',
            'products.*.price' => 'required|numeric',
        ]);
    
        // Set max bundle count based on user badge or as 3 for unauthenticated users
        $maxBundleCount = (Auth::check()) ? PHP_INT_MAX : 3; // Default to 3 for non-logged users
    
        if (Auth::check()) {
            $user = User::find($userId);
            switch ($user->badge) {
                case 'Silver':
                    $maxBundleCount = 3; // Max for Silver
                    break;
                case 'Gold':
                    $maxBundleCount = 5; // Max for Gold
                    break;
            }
        }
    
        // Count unique bundles in cart using the unique user ID
        $currentBundleCount = Cart::where('user_id', $userId)
            ->with('product') // Load the product relationship
            ->select('product_sku') // Select only the product SKU
            ->distinct() // Get distinct product SKUs
            ->get()
            ->unique('product.Bundle') // Filter unique bundles based on the product's Bundle attribute
            ->count();
    
        // Log the user's badge and current bundle count
        \Log::info("User ID: $userId, Max Bundle Count: $maxBundleCount, Current Unique Bundle Count: $currentBundleCount");
    
        // Check if user has reached the max bundle count
        if ($currentBundleCount >= $maxBundleCount) {
            return redirect()->route('home')->with('error', 'Bundle count limit is already reached.');
        }
    
        // Create a new cart entry for each product
        try {
            foreach ($request->products as $sku => $product) {
                // Check if the product is already in the cart
                $existingCartItem = Cart::where('user_id', $userId)
                    ->where('product_sku', $sku)
                    ->first();
    
                if ($existingCartItem) {
                    // If the product is already in the cart, redirect with an error
                    return redirect()->route('home')->with('error', 'You already have this bundle in your cart.');
                }
    
                // Create the cart entry
                Cart::create([
                    'user_id' => $userId, // Will be the session ID if not logged in
                    'product_sku' => $sku,
                    'quantity' => $product['quantity'],
                    'price_type' => $product['price_type'],
                    'price' => $product['price'],
                    'added_at' => now(),
                ]);
            }
    
            return redirect()->route('home')->with('success', 'Bundle Added to Cart Successfully!');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add items to cart.'], 500);
        }
    }
    
    
    
    // DELETE SELECTED CART ITEMS
    public function deleteSelected(Request $request)
    {
        /*
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first');
            }
        */
    
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:usc_carts,id',
        ]);
    
        // Check if there are no IDs selected
        if (!isset($request->ids) || count($request->ids) === 0) {
            return redirect()->route('home')->with('error', 'No items were selected for deletion.');
        }
    
        try {
            Cart::whereIn('id', $request->ids)->delete();
    
            return redirect()->route('home')->with('success', 'Selected items deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Failed to delete selected items. Please try again.');
        }
    }
    


    public function addQuantity($cartId)
    {
        // Find the cart item
        $cart = Cart::findOrFail($cartId);

        // Increase the quantity
        $cart->quantity += 1;
        $cart->save(); // Save the updated quantity

        return redirect()->route('cart.index')->with('success', 'Quantity increased!');
    }



    public function subQuantity($cartId)
    {
        // Find the cart item
        $cart = Cart::findOrFail($cartId);

        // Decrease the quantity if greater than 1
        if ($cart->quantity > 1) {
            $cart->quantity -= 1;
            $cart->save(); // Save the updated quantity
        }

        return redirect()->route('cart.index')->with('success', 'Quantity decreased!');
    }
}

