<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class SaleBreakdownController extends Controller
{
    /**
     * Display the sale breakdown page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the store ID and filter from the request
        $store_id = $request->get('store_id');
        $filter = $request->get('filter', 'daily'); // Default to 'daily' if no filter is provided
    
        // Check if the authenticated user is the owner of the store
        $store = Store::where('id', $store_id)
            ->where('store_owner', auth()->user()->id)
            ->first();
    
        // If no store is found or the authenticated user is not the owner, redirect with an error
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have authority to access this sales list');
        }
    
        // Initialize the query for sales data based on the store ID
        $salesQuery = Sale::where('sale_made', $store_id);
    
        // Filter the sales query based on the selected filter
        if ($filter == 'daily') {
            // Filter sales by the current day (ignore time, compare only date part)
            $day = $request->get('day');
            $salesQuery->whereDate('createdAt', '=', $day);  // Compare only the date part (YYYY-MM-DD)
        } elseif ($filter == 'monthly') {
            // Filter sales by the selected month (ignore time, compare only the month and year)
            $month = $request->get('month');
            $year = $request->get('year', date('Y'));  // Default to the current year if no year is passed
            $salesQuery->whereMonth('createdAt', '=', $month)
                ->whereYear('createdAt', '=', $year);
        } elseif ($filter == 'yearly') {
            // Filter sales by the selected year
            $year = $request->get('year');
            $salesQuery->whereYear('createdAt', '=', $year);
        }
    
        // Execute the sales query to get the filtered sales data
        $sales = $salesQuery->get();
    
        // Initialize an array to store breakdown data
        $breakdown = [];
    
        // Loop through each sale and decode the ordered_items JSON
        foreach ($sales as $sale) {
            $orderedItems = json_decode($sale->ordered_items, true);
            
            foreach ($orderedItems as $item) {
                $productBundleId = $item['product_bundle_id'];
                $subTotal = (float) $item['sub_total'];
                $quantity = (int) $item['quantity'];
                $consignTotal = (float) $item['consign_total'];  // Adding consign_total
                
                // Calculate total_profit (subTotal - consignTotal)
                $totalProfit = $subTotal - $consignTotal;  // This is not stored in the database, just calculated
                
                // If the product_bundle_id is already in the breakdown, update the totals
                if (isset($breakdown[$productBundleId])) {
                    $breakdown[$productBundleId]['total'] += $subTotal;
                    $breakdown[$productBundleId]['quantity'] += $quantity;
                    $breakdown[$productBundleId]['consign_total'] += $consignTotal;  // Update consign_total
                    $breakdown[$productBundleId]['total_profit'] += $totalProfit;  // Update total_profit
                } else {
                    // Initialize the totals for this product_bundle_id
                    $breakdown[$productBundleId] = [
                        'total' => $subTotal,
                        'quantity' => $quantity,
                        'consign_total' => $consignTotal,  // Initialize consign_total
                        'total_profit' => $totalProfit,    // Initialize total_profit
                    ];
                }
            }
        }              
    
        // Pass the breakdown data and store_id to the view
        return view('pages.saleBreakdown', [
            'breakdown' => $breakdown,
            'filter' => $filter,
            'store_id' => $store_id,
        ]);
    }      
}
