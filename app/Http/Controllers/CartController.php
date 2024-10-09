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
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your cart.');
        }
    
        // Retrieve the cart items for the authenticated user
        $carts = Cart::where('user_id', Auth::id())->get();
    
        // Check if there are no cart items
        if ($carts->isEmpty()) {
            return redirect()->route('home')->with('error', 'There are no items in your cart.');
        }
    
        return view('pages.cartPage', compact('carts'));
    }    

    // ADD TO CART
    public function add(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
    
        // Validate incoming request
        $request->validate([
            'products' => 'required|array',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price_type' => 'required|string',
            'products.*.price' => 'required|numeric',
        ]);
    
        // Get the authenticated user's ID
        $userId = Auth::id();
    
        // Retrieve the user model
        $user = User::find($userId);
    
        // Set max bundle count based on user badge
        $maxBundleCount = 0; // Initialize max bundle count
        switch ($user->badge) {
            case 'Silver':
                $maxBundleCount = 3; // Max for Silver
                break;
            case 'Gold':
                $maxBundleCount = 5; // Max for Gold
                break;
            default:
                $maxBundleCount = PHP_INT_MAX; // No limit for other badges
        }
    
        // Count unique bundles in cart using the Product relationship
        $currentBundleCount = Cart::where('user_id', $userId)
            ->with('product') // Load the product relationship
            ->select('product_sku') // Select only the product SKU
            ->distinct() // Get distinct product SKUs
            ->get()
            ->unique('product.Bundle') // Filter unique bundles based on the product's Bundle attribute
            ->count();
    
        // Log the user's badge, maximum bundle count, and current bundle count
        \Log::info("User ID: $userId, Badge: {$user->badge}, Max Bundle Count: $maxBundleCount, Current Unique Bundle Count: $currentBundleCount");
    
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
    
                Cart::create([
                    'user_id' => $userId,
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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
    
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
}

