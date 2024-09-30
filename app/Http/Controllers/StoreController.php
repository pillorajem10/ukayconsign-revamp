<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;


class StoreController extends Controller
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
        $query = Store::query();
    
        // Optional: Filtering by status
        if ($request->filled('status')) {
            $query->where('store_status', $request->status);
        }
    
        // Optional: Sorting by name
        $stores = $query->orderBy('store_name')->paginate(10);
    
        return view('pages.stores', compact('stores'));
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
