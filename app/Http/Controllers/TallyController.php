<?php

namespace App\Http\Controllers;

use App\Models\Tally;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TallyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // If no store_id is provided in the request, redirect back with an error
        if (!$request->filled('store_id')) {
            return redirect()->route('home')->with('error', 'Store id not found.');
        }
    
        // Check if the provided store_id exists and belongs to the authenticated user
        $store = Store::where('id', $request->store_id)
                      ->where('store_owner', $user->id)
                      ->first();
    
        // If the store doesn't exist or doesn't belong to the user, redirect with an error message
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have the authority to access that store.');
        }
    
        // Default to daily if no filter is provided
        $filter = $request->get('filter', 'daily');  // default filter is 'daily'
    
        $query = Tally::with('store')
                      ->where('store_id', $request->store_id)
                      ->orderBy('createdAt', 'desc');  // Sort by createdAt in descending order
    
        if ($filter == 'monthly') {
            // Get the SUM of total for each month based on the 'month' field in your table
            $tallies = Tally::selectRaw('SUM(total) as total, month, year')
                            ->where('store_id', $request->store_id)
                            ->whereBetween('year', [2023, date('Y')]) // Include years from 2023 to current year (2024)
                            ->groupBy('month', 'year')  // Group by month and year
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->paginate(10);
        } elseif ($filter == 'yearly') {
            // Get the SUM of total for each year based on the 'year' field in your table
            $tallies = Tally::selectRaw('SUM(total) as total, year')
                            ->where('store_id', $request->store_id)
                            ->whereBetween('year', [2023, 2025]) // Include years from 2023 to current year (2024)
                            ->groupBy('year')  // Group by year
                            ->orderBy('year', 'desc')
                            ->paginate(10);
        } else {
            // Default to daily, show tallies by day
            $tallies = $query->paginate(10);  // Paginate daily tallies
        }
        
        // Return the view with the tallies data and the selected filter
        return view('pages.tallies', compact('tallies', 'store', 'filter'));
    }         
}

