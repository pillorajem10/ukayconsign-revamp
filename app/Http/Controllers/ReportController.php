<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); // Fetch authenticated user
        $stores = Store::where('store_owner', $user->id)->get(); // Fetch user's stores
    
        // Get the selected store ID and month from the request
        $selectedStoreId = $request->input('store_id', '');
        $selectedMonth = $request->input('month', '');
    
        // If no store is selected and the user has stores, set the default to the first store's ID
        if (empty($selectedStoreId) && $stores->isNotEmpty()) {
            $selectedStoreId = $stores->first()->id; // Set default store to the first store owned by the user
        }
    
        // Check if the selected store ID is valid and belongs to the user
        if ($selectedStoreId && $selectedStoreId !== 'all') {
            $store = Store::where('id', $selectedStoreId)
                ->where('store_owner', $user->id)
                ->first();
    
            if (!$store) {
                return redirect()->route('home')->with('error', 'You don\'t have authority to access this sales list');
            }
        }
    
        // Initialize arrays to hold monthly sales data
        $monthlySales = [];
        $orderedItemsSales = [];
        $quantityPerBundle = []; // New array to hold quantities per product bundle
    
        // Prepare the query for total sales based on the selected store
        $query = Sale::whereYear('date_of_transaction', Carbon::now()->year);
    
        if ($selectedStoreId === 'all') {
            $query->whereIn('sale_made', $stores->pluck('id')->toArray());
        } else {
            $query->where('sale_made', $selectedStoreId);
        }
    
        // Get the sales data grouped by month for total sales (no month filter)
        $salesData = $query->selectRaw('MONTH(date_of_transaction) as month, SUM(total) as total_sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    
        // Populate the monthly sales array
        for ($month = 1; $month <= 12; $month++) {
            $monthlySales[$month] = 0; // Default to 0
        }
    
        foreach ($salesData as $sale) {
            $monthlySales[$sale->month] = $sale->total_sales;
        }
    
        // Now fetch the ordered items subtotal by product_bundle_id
        $orderedItemsQuery = Sale::whereYear('date_of_transaction', Carbon::now()->year);
    
        if ($selectedStoreId === 'all') {
            $orderedItemsQuery->whereIn('sale_made', $stores->pluck('id')->toArray());
        } else {
            $orderedItemsQuery->where('sale_made', $selectedStoreId);
        }
    
        // Apply the month filter only to ordered items
        if ($selectedMonth) {
            $orderedItemsQuery->whereMonth('date_of_transaction', $selectedMonth);
        }
    
        // Fetch ordered items and calculate subtotal
        $orderedItemsData = $orderedItemsQuery->get();
    
        foreach ($orderedItemsData as $sale) {
            $items = json_decode($sale->ordered_items, true);
            foreach ($items as $item) {
                // Calculate subtotal
                $bundleId = $item['product_bundle_id'];
                $subTotal = (float)$item['sub_total'];
    
                if (!isset($orderedItemsSales[$bundleId])) {
                    $orderedItemsSales[$bundleId] = 0;
                }
                $orderedItemsSales[$bundleId] += $subTotal;
    
                // Sum quantities per product bundle ID
                if (!isset($quantityPerBundle[$bundleId])) {
                    $quantityPerBundle[$bundleId] = 0;
                }
                $quantityPerBundle[$bundleId] += $item['quantity']; // Add quantity
            }
        }
    
        return view('pages.reports', compact('stores', 'monthlySales', 'orderedItemsSales', 'quantityPerBundle'));
    }
    
}
