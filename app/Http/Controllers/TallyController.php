<?php

namespace App\Http\Controllers;

use App\Models\Tally;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TallyController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $stores = Store::where('store_owner', $user->id)->get();
        
        $query = Tally::with('store')
                      ->whereIn('store_id', $stores->pluck('id'));
        
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            
            $query->whereBetween('createdAt', [$startDate, $endDate]);
        }
    
        // Add store filter
        if ($request->has('store_id') && $request->store_id != '') {
            $query->where('store_id', $request->store_id);
        }
        
        $tallies = $query->get();
    
        return view('pages.tallies', compact('tallies', 'stores'));
    }            
}

