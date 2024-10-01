<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CheckoutController extends Controller
{
    // Display the checkout page
    public function index()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to proceed to checkout.');
        }

        // Retrieve the cart items for the authenticated user
        $carts = Cart::where('user_id', Auth::id())->get();

        return view('pages.checkout', compact('carts'));
    }
}
