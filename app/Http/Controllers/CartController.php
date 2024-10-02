<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
    
        // Create a new cart entry for each product
        try {
            foreach ($request->products as $sku => $product) {
                // Check if the product is already in the cart
                $existingCartItem = Cart::where('user_id', Auth::id())
                    ->where('product_sku', $sku)
                    ->first();
    
                if ($existingCartItem) {
                    // If the product is already in the cart, redirect with an error
                    return redirect()->route('home')->with('error', 'You already have this bundle in your cart.');
                }
    
                Cart::create([
                    'user_id' => Auth::id(), // Get the authenticated user's ID
                    'product_sku' => $sku, // Use the SKU from the key
                    'quantity' => $product['quantity'],
                    'price_type' => $product['price_type'],
                    'price' => $product['price'],
                    'added_at' => now(), // Current timestamp
                ]);
            }
    
            // Redirect back to home with a success message
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
            'ids.*' => 'integer|exists:carts,id',
        ]);
    
        // Log the incoming request data for debugging
        Log::info('Request data: ', $request->all());
    
        // Check if there are no IDs selected
        if (!isset($request->ids) || count($request->ids) === 0) {
            return redirect()->route('home')->with('error', 'No items were selected for deletion.');
        }
    
        try {
            // Log the IDs being requested for deletion
            Log::info('User ' . Auth::id() . ' is requesting to delete cart items: ', $request->ids);
            
            // Proceed to delete the items
            Cart::whereIn('id', $request->ids)->delete();
    
            return redirect()->route('home')->with('success', 'Selected items deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete selected items for user ' . Auth::id() . ': ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Failed to delete selected items. Please try again.');
        }
    }           
}

