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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
