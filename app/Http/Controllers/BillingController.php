<?php

namespace App\Http\Controllers;

use App\Models\Billing;  // Make sure to import the Billing model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }
    
    /**
     * Display a listing of the billings for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();  // Get the authenticated user's ID

        // Retrieve billing records that match the user's ID
        $billings = Billing::where('user_id', $userId)->get();

        // Return the view and pass the $billings data to the Blade template
        return view('pages.billingList', compact('billings'));
    }

    /**
     * Display the breakdown of a specific billing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Retrieve the billing record by its ID
        $billing = Billing::findOrFail($id);

        // Decode the JSON stored in 'billing_breakdown' to get the product details
        $billingBreakdown = json_decode($billing->billing_breakdown, true);

        // Return the view and pass the $billing and $billingBreakdown data
        return view('pages.billingBreakdown', compact('billing', 'billingBreakdown'));
    }
}
