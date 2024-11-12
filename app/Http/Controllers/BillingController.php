<?php

namespace App\Http\Controllers;

use App\Models\Billing;
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
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the billing record by its ID
        $billing = Billing::findOrFail($id);

        // Check if the billing record belongs to the authenticated user
        if ($billing->user_id !== $userId) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have the authority to access that billing.');
        }

        // Decode the JSON stored in 'billing_breakdown' to get the product details
        $billingBreakdown = json_decode($billing->billing_breakdown, true);

        // Return the view and pass the $billing and $billingBreakdown data
        return view('pages.billingBreakdown', compact('billing', 'billingBreakdown'));
    }

    /**
     * Show the form for uploading proof of payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUploadProofOfPayment($id)
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the billing record by its ID
        $billing = Billing::findOrFail($id);

        // Check if the billing record belongs to the authenticated user
        if ($billing->user_id !== $userId) {
            return redirect()->route('billings.index')->with('error', 'You don\'t have the authority to access that billing.');
        }

        // Return the Blade view with the billing record
        return view('pages.uploadProofOfBilling', compact('billing'));
    }

    /**
     * Update the proof of payment and payment platform for a billing record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePayment(Request $request, $id)
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::id();

        // Retrieve the billing record by its ID
        $billing = Billing::findOrFail($id);

        // Check if the billing record belongs to the authenticated user
        if ($billing->user_id !== $userId) {
            return redirect()->route('billings.index')->with('error', 'You don\'t have the authority to update this billing.');
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'payment_platform' => 'required|string|max:255',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:10240', // Max 10MB
        ]);

        // Update the payment platform field
        $billing->payment_platform = $validated['payment_platform'];

        // Handle the proof of payment file if it is provided
        if ($request->hasFile('proof_of_payment')) {
            // Get the file content and encode it in base64
            $file = $request->file('proof_of_payment');
            $fileContent = file_get_contents($file->getRealPath());
            $base64Content = base64_encode($fileContent);

            // Store the base64 encoded content in the proof_of_payment field
            $billing->proof_of_payment = $base64Content;
        }

        // Save the updated billing record
        $billing->save();

        // Redirect back with a success message
        return redirect()->route('billings.index')->with('success', 'Proof of payment uploaded successfully.');
    }
}
