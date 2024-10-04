<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Get store_id from the query string
        $storeId = $request->input('store_id');
    
        // Retrieve sales records filtered by store_id if it exists
        $sales = Sale::when($storeId, function ($query) use ($storeId) {
            return $query->where('sale_made', $storeId);
        })->get();
    
        // Check if sales are empty
        if ($sales->isEmpty()) {
            return redirect()->route('home')->with('error', 'Cannot get sale of this store');
        }
    
        return view('pages.sales', compact('sales'));
    }      
}
