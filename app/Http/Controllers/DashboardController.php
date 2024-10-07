<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Promos;
use App\Models\Store;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }

    public function index()
    {
        // Get the authenticated user's details
        $user = Auth::user(); // Fetch all user details
    
        // Fetch promos
        $promos = Promos::all(); // Retrieve all promos

        // Fetch stores for the authenticated user
        $stores = Store::where('store_owner', $user->id)->get(); // Filter stores by the authenticated user's ID

        // Return the dashboard view with the user's details, promos, and stores
        return view('pages.dashboard', compact('user', 'promos', 'stores')); // Pass the user, promos, and stores to the view
    }    
}

