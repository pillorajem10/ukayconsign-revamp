<?php

namespace App\Http\Controllers;

use App\Models\UscReturn;
use App\Models\Store;
use App\Models\Product;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UscReturnController extends Controller
{
    // Show the form for creating a new return
    public function create(Request $request)
    {
        $productSku = $request->input('product_sku');
        $storeId = $request->input('store_id');
        
        // Check if the authenticated user has access to the specified store
        $authId = Auth::id();
        $store = Store::where('id', $storeId)
                      ->where('store_owner', $authId)
                      ->first();
    
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have the authority to access that store.');
        }
    
        // Check if the SKU exists in the StoreInventory for the specified store
        $inventoryItem = StoreInventory::where('SKU', $productSku)
                                        ->where('store_id', $storeId)
                                        ->first();
    
        if (!$inventoryItem) {
            return redirect()->route('store-inventory.index', ['store_id' => $storeId])
                             ->with('error', 'The specified product is not available in the store inventory.');
        }
    
        // Fetch the store name
        $storeName = $store->store_name; // Use the fetched store object
        $productName = $inventoryItem->ProductID ?? 'Unknown Product'; // Get ProductID from inventory
    
        return view('pages.returnRequest', compact('productSku', 'storeId', 'storeName', 'productName'));
    }    

    // Store a newly created return
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'product_sku' => 'required|string',
            'store_id' => 'required|integer|exists:stores,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        // Check the stock for the requested product in the specified store
        $storeInventory = StoreInventory::where('SKU', $request->product_sku)
            ->where('store_id', $request->store_id)
            ->first();
    
        if (!$storeInventory || $storeInventory->Stocks < $request->quantity) {
            return redirect()->route('store-inventory.index', ['store_id' => $request->store_id])
                ->with('error', 'You do not have enough stock to return this quantity.');
        }
    
        // Prepare the data for creation, setting return_status to 'Processing'
        $data = $request->all();
        $data['return_status'] = 'Processing';
    
        $return = UscReturn::create($data);
    
        return redirect()->route('store-inventory.index', ['store_id' => $request->store_id])
            ->with('success', 'Return request submitted successfully!');
    }

    // Index method to list all return requests filtered by store_id
    public function index(Request $request)
    {
        // Ensure the user is authenticated
        $authId = Auth::id();
    
        // Get the store_id from the request (from URL or form data)
        $storeId = $request->input('store_id');
    
        // Fetch the store the user owns, or deny access if it's not their store
        $store = Store::where('id', $storeId)
                      ->where('store_owner', $authId)
                      ->first();
    
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have the authority to access that store.');
        }
    
        // Fetch return requests filtered by store_id and paginate (limit 10)
        $returns = UscReturn::with(['user', 'store', 'product'])
                            ->where('store_id', $storeId)
                            ->paginate(10);  // 10 items per page
    
        // Pass the returns data and storeId to the view
        return view('pages.returnRequestList', compact('returns', 'storeId'));
    } 
    
    public function receivedBackItems($returnId, Request $request)
    {
        // Get the authenticated user ID
        $authId = Auth::id();
        
        // Get the store_id from the request (from URL or form data)
        $storeId = $request->input('store_id');
        
        // Fetch the store the user owns, or deny access if it's not their store
        $store = Store::where('id', $storeId)
                      ->where('store_owner', $authId)
                      ->first();
        
        // If the store does not exist or does not belong to the user, deny access
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have the authority to access that store.');
        }
    
        // Get the return by ID
        $return = UscReturn::findOrFail($returnId);
    
        // Ensure the return status is valid to update
        if ($return->return_status != 'Shipped Back To Store') {
            return redirect()->route('usc-returns.index', ['store_id' => $return->store_id])
                             ->with('error', 'The return is not in a valid state for receiving back.');
        }
    
        // Update the return status to "Received By Store"
        $return->return_status = 'Received By Store';
        $return->save();
    
        // Update inventory (add the quantity back to store inventory)
        $storeId = $return->store_id;
        $productSku = $return->product_sku;
        $quantityToAdd = $return->quantity;
    
        // Find the inventory record for the store and product SKU
        $inventory = StoreInventory::where('store_id', $storeId)
                                    ->where('SKU', $productSku)
                                    ->first();
    
        // If inventory record is found, add back the return quantity
        if ($inventory) {
            $inventory->Stocks += $quantityToAdd;
            $inventory->save();  // Save updated inventory
        } else {
            // If no inventory record found, return an error message
            return redirect()->route('usc-returns.index', ['store_id' => $return->store_id])
                             ->with('error', 'Inventory record not found.');
        }
    
        // Redirect back with a success message
        return redirect()->route('usc-returns.index', ['store_id' => $return->store_id])
                         ->with('success', 'Return received and inventory updated successfully.');
    }
    
}

