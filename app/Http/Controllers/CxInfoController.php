<?php

namespace App\Http\Controllers;

use App\Models\CxInfo;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CxInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }





    public function index(Request $request)
    {
        $authId = Auth::id();
        
        // Check if store_id is provided in the request
        if (!$request->filled('store_id')) {
            return redirect()->route('home')->with('error', 'Store ID not found.');
        }
    
        // Check if the store exists and the user has access to it
        $store = Store::where('id', $request->store_id)
                      ->where('store_owner', $authId)
                      ->first();
        
        if (!$store) {
            return redirect()->route('home')->with('error', 'You don\'t have the authority to access this store.');
        }
    
        // Filter CxInfo by store_id
        $cxInfos = CxInfo::where('store_id', $request->store_id)->get();
    
        // Pass the CxInfo data to the view
        return view('pages.cxInfos', compact('cxInfos', 'store'));
    }    
}
