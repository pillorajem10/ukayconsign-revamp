<?php

namespace App\Http\Controllers;

use App\Models\StoreInventory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }
    
    public function index(Request $request)
    {
        // Get the authenticated user ID
        $authId = Auth::id();
        
        // Check if store_id is provided in the request
        if (!$request->filled('store_id')) {
            return redirect()->route('home')->with('error', 'Store id not found.');
        }
    
        $query = StoreInventory::query();
        
        // Check if the store_id is valid and belongs to the authenticated user
        $store = Store::where('id', $request->store_id)
                      ->where('store_owner', $authId)
                      ->first();
    
        if (!$store) {
            return redirect()->route('home')->with('error', 'You don\'t have the authority to access that store.');
        }
    
        $query->where('store_id', $request->store_id);
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('ProductID', 'like', '%' . $request->search . '%');
        }
    
        $inventory = $query->get(); // Fetch all records without pagination
        return view('pages.storeInventory', compact('inventory'));
    }
          
    
    /**
     * 
     */
    public function update(Request $request, $id)
    {
        // Validate the request input
        $request->validate([
            'SPR' => 'required|numeric', // Example validation for SPR
        ]);
    
        // Find the inventory item by ID
        $inventoryItem = StoreInventory::findOrFail($id);
    
        // Update the SPR field
        $inventoryItem->SPR = $request->input('SPR');
        $inventoryItem->save(); // Save the updated record
    
        // Redirect back to the store inventory page with a success message
        return redirect()->route('store-inventory.index', ['store_id' => $request->store_id])
            ->with('success', 'SRP Updated successfully');
    }    
}
