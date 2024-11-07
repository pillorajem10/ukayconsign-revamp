<?php

namespace App\Http\Controllers;

use App\Models\CxInfo;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailBlastMail; // Make sure to create this Mailable
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
    
        // Get the filter value from the request
        $interestFilter = $request->input('interest_filter');
    
        // Filter CxInfo by store_id and optionally by interest
        $cxInfosQuery = CxInfo::where('store_id', $request->store_id);
    
        if ($interestFilter) {
            $cxInfosQuery->where('interest', $interestFilter);
        }
    
        $cxInfos = $cxInfosQuery->get();
    
        // Pass the CxInfo data and store info to the view
        return view('pages.cxInfos', compact('cxInfos', 'store'));
    }
        

    public function sendBlastEmails(Request $request)
    {
        // Validate the request
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
    
        // Retrieve the store using the store_id from the request
        $store = Store::find($request->input('store_id'));  // Use the store_id from the request
    
        if (!$store) {
            return redirect()->route('cxInfos.index')
                             ->with('error', 'Store not found or you do not have permission to send an email blast.');
        }
    
        // Start building the query to get customers associated with this store
        $cxInfosQuery = CxInfo::where('store_id', $store->id);
    
        // Apply the interest filter if it exists in the request
        if ($request->has('interest_filter') && $request->interest_filter) {
            // Only filter if the interest_filter is set (not empty)
            $cxInfosQuery->where('interest', $request->interest_filter);
        }
    
        // Retrieve the filtered customer data
        $cxInfos = $cxInfosQuery->get();
    
        // Loop through all customers and send the email blast
        foreach ($cxInfos as $cxInfo) {
            if ($cxInfo->email) { // Only send email if the customer has an email address
                Mail::to($cxInfo->email)->send(new EmailBlastMail($request->subject, $request->body, $store->store_name));
            }
        }
    
        // Redirect with a success message
        return redirect()->route('cxInfos.index', ['store_id' => $store->id])
                         ->with('success', 'Email blast sent successfully!');
    }      
}

