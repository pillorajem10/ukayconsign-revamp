<?php

namespace App\Http\Controllers;

use App\Models\StoreInventory;
use App\Models\Store;
use Illuminate\Http\Request;

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
        $query = StoreInventory::query();
        
        // Filter by store_id if provided
        if ($request->filled('store_id')) {
            // Check if the store_id is valid
            $store = Store::find($request->store_id);
            if (!$store) {
                return redirect()->route('home')->with('error', 'Store Id is not valid.');
            }
    
            $query->where('store_id', $request->store_id);
        }
    
        // Optionally add any other filters or sorting here
    
        $inventory = $query->paginate(10);
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
