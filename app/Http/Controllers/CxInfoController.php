<?php

namespace App\Http\Controllers;

use App\Models\CxInfo;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailBlastMail; // Make sure to create this Mailable

class CxInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }

    public function index(Request $request)
    {
        $authId = auth()->id();
        
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
    
        // Retrieve all the customers associated with this store
        $cxInfos = CxInfo::where('store_id', $store->id)->get();
    
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

