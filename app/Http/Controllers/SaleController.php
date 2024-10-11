<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Store;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Get store_id from the query string
        $storeId = $request->input('store_id');

        // Check if the authenticated user is the owner of the store
        $store = Store::where('id', $storeId)
            ->where('store_owner', auth()->user()->id)
            ->first();

        if (!$store) {
            return redirect()->route('home')->with('error', 'You don\'t have authority to access this sales list');
        }

        // Retrieve sales records filtered by store_id
        $sales = Sale::when($storeId, function ($query) use ($storeId) {
            return $query->where('sale_made', $storeId);
        })->get();

        // Check if sales are empty
        if ($sales->isEmpty()) {
            return redirect()->route('home')->with('error', 'You haven\'t made any sale yet.');
        }

        return view('pages.sales', compact('sales'));
    }
}
