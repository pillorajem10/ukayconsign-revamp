<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Promos;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Order;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }

    public function index()
    {
        // Get the authenticated user's details
        $user = Auth::user(); // Fetch all user details
        
        // Fetch promos
        $promos = Promos::all(); // Retrieve all promos
    
        // Fetch stores for the authenticated user
        $stores = Store::where('store_owner', $user->id)->get(); // Filter stores by the authenticated user's ID
    
        // Get the store IDs
        $storeIds = $stores->pluck('id')->toArray(); // Extract the IDs of the stores
    
        // Fetch sales for the stores owned by the user
        $sales = Sale::whereIn('sale_made', $storeIds)->get(); // Filter sales by store IDs

        // Fetch orders for the authenticated user
        $orders = Order::where('user_id', $user->id)->get(); // Filter orders by user ID
    
        // Initialize an array to count occurrences of each product
        $productCounts = [];
    
        foreach ($sales as $sale) {
            // Decode the ordered_items JSON string into an array
            $orderedItems = json_decode($sale->ordered_items, true);
            
            // Count the occurrences of each product_sku and map to product_bundle_id
            foreach ($orderedItems as $item) {
                $sku = $item['product_sku'];
                $bundleId = $item['product_bundle_id'];
                $quantity = $item['quantity']; // Get the quantity sold
    
                // Increment the total quantity for this SKU
                if (isset($productCounts[$sku])) {
                    $productCounts[$sku]['count'] += $quantity; // Add quantity to the existing count
                } else {
                    $productCounts[$sku] = [
                        'count' => $quantity, // Initialize count with the quantity
                        'product_bundle_id' => $bundleId,
                    ];
                }
            }
        }
    
        // Sort products by their total quantity sold in descending order and get the top products
        arsort($productCounts);
        $mostSoldProducts = array_slice($productCounts, 0, 5, true); // Get top 5 most sold products
    

    
        // Return the dashboard view with the user's details, promos, stores, most sold products, and orders
        return view('pages.dashboard', compact('user', 'promos', 'stores', 'mostSoldProducts', 'orders'));
    }            
}