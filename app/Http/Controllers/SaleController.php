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
            return redirect()->route('dashboard')->with('error', 'You don\'t have authority to access this sales list');
        }
    
        // Initialize the sales query
        $salesQuery = Sale::where('sale_made', $storeId);
    
        // Handle filtering by date if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
            $salesQuery->whereBetween('date_of_transaction', [$startDate, $endDate]);
        }
    
        // Retrieve sales records with pagination
        $sales = $salesQuery->paginate(10); // Display 10 sales per page
    
        return view('pages.sales', compact('sales', 'storeId'));
    }    
}
