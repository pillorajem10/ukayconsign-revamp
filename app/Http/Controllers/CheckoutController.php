<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;

// use Illuminate\Support\Facades\Log;

use App\Mail\OrderConfirmationMail; 
use App\Mail\AdminOrderNotification; 
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    // Display the checkout page
    public function index()
    {
        // Retrieve the user ID; use session ID if not logged in
        $userId = Auth::check() ? Auth::id() : session()->getId();
    
        // Retrieve the cart items based on user ID or session ID
        $carts = Cart::where('user_id', $userId)->get();
    
        // Check if the cart is empty
        if ($carts->isEmpty()) {
            return redirect()->route('home')->with('error', 'Your cart is empty, there is nothing to checkout.');
        }
    
        // Get existing store information, set to null if not logged in
        $store = Auth::check() ? Store::where('store_owner', Auth::id())->first() : null;
    
        // Get the most recent order information, set to null if not logged in
        $latestOrder = Auth::check() ? Order::where('user_id', Auth::id())->orderBy('createdAt', 'desc')->first() : null;
    
        return view('pages.checkout', compact('carts', 'store', 'latestOrder'));
    }    
        

    public function store(Request $request)
    {
        // Validate request
        if (!Auth::check()) {
            \Log::info('Incoming request data NOT AUTHENTICATED', [
                'request_data' => $request->all(),
            ]);
            // Validation rules for unauthenticated users
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'store_name' => 'required|string|max:255',
                'store_address' => 'required|string|max:255',
                'store_phone_number' => 'required|string|max:20',
                'phone_number' => 'required|string|max:20',
                // 'store_email' => 'required|email|max:255',
                'store_fb_link' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|min:8',
                'estimated_items_sold_per_month' => 'nullable|integer|min:0', // Add validation for this field
                'fb_link' => 'nullable|string|max:255',
                'government_id_card' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'proof_of_billing' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'selfie_uploaded' => 'required|file|mimes:jpg,png|max:2048',
            ], [
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'address.required' => 'Address is required.',
                'store_name.required' => 'Store name is required.',
                'store_address.required' => 'Store address is required.',
                'store_phone_number.required' => 'Store phone number is required.',
                'phone_number.required' => 'Phone number is required.',
                // 'store_email.required' => 'Store email is required.',
                'store_fb_link.required' => 'Store Facebook link is required.',
                'email.required' => 'Email is required.',
                'email.unique' => 'This email is already registered.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'estimated_items_sold_per_month.integer' => 'Estimated items sold must be a number.',
                'estimated_items_sold_per_month.min' => 'Estimated items sold must be at least 0.',
                'government_id_card.required' => 'Government ID card is required.',
                'proof_of_billing.required' => 'Proof of billing is required.',
                'selfie_uploaded.required' => 'Selfie upload is required.',
            ]);            
        } else {
            // Validation rules for authenticated users
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'store_name' => 'required|string|max:255',
                'store_address' => 'required|string|max:255',
                'store_phone_number' => 'required|string|max:20',
                // 'store_email' => 'required|email|max:255',
                'store_fb_link' => 'required|string|max:255',
            ]);
        }        
        
        // Check if user is authenticated
        $user = Auth::user();
        
        // Use user ID if authenticated, otherwise use session ID
        $userId = $user ? $user->id : session()->getId(); // Use session ID if not logged in
    
    
        // Retrieve the cart items for the user or session
        $carts = Cart::where('user_id', $userId)->get();
    
        // Log the retrieved carts
    
        if ($carts->isNotEmpty()) {
            // Handle user registration if not authenticated
            if (!$user) {
                // Check if email already exists
                if (User::where('email', $request->email)->exists()) {
                    return redirect()->back()->with('error', 'The email address is already in use.')->withInput();
                }
    
                // Create new user
                $user = User::create([
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'fname' => $request->first_name,
                    'lname' => $request->last_name,
                    'verified' => true,
                    'fb_link' => $request->fb_link,
                    'phone_number' => $request->phone_number,
                    'estimated_items_sold_per_month' => $request->estimated_items_sold_per_month,
                    'government_id_card' => file_get_contents($request->file('government_id_card')->getRealPath()), // Read file as binary
                    'proof_of_billing' => file_get_contents($request->file('proof_of_billing')->getRealPath()), // Read file as binary
                    'selfie_uploaded' => file_get_contents($request->file('selfie_uploaded')->getRealPath()), // Read file as binary
                ]);                              
            } else {
                // Update user's fname and lname only if they are NULL
                if (is_null($user->fname)) {
                    $user->fname = $request->first_name;
                }
    
                if (is_null($user->lname)) {
                    $user->lname = $request->last_name;
                }
    
                // Save changes to the user
                $user->save();
            }
    
            // Check for existing store by store_name
            $existingStore = Store::where('store_name', $request->store_name)->first();
    
            if ($existingStore) {
                // Update the existing store information
                $existingStore->update([
                    'store_address' => $request->store_address,
                    'store_phone_number' => $request->store_phone_number,
                    // 'store_email' => $request->store_email,
                    'store_fb_link' => $request->store_fb_link,
                ]);
                $storeId = $existingStore->id; // Get the existing store ID
            } else {
                // Create new store information
                $newStore = Store::create([
                    'store_name' => $request->store_name,
                    'store_owner' => $user->id,
                    'store_address' => $request->store_address,
                    'store_fb_link' => $request->store_fb_link,
                    'store_phone_number' => $request->store_phone_number,
                    // 'store_email' => $request->store_email,
                    'store_total_earnings' => 0,
                    'store_status' => 'active',
                ]);
                $storeId = $newStore->id; // Get the new store ID
            }
    
            // Build the productsOrdered array with store_id
            $productsOrdered = $carts->map(function ($cart) use ($storeId) {
                return [
                    'cart_id' => $cart->id,
                    'bundle_name' => $cart->product->Bundle,
                    'product_sku' => $cart->product->SKU,
                    'product_srp' => $cart->product->SRP,
                    'product_id' => $cart->product->ProductID,
                    'product_consign' => $cart->product->Consign,
                    'category' => $cart->product->Category,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price,
                    'store_id' => $storeId, // Use the store ID here
                ];
            });
    
            // Create the order
            $order = Order::create([
                'first_name' => $user->fname,
                'last_name' => $user->lname,
                'user_id' => $user->id, // Use the newly registered user's ID
                'products_ordered' => json_encode($productsOrdered),
                'address' => $request->address,
                'store_name' => $request->store_name,
                'email' => $user->email,
                'total_price' => $carts->sum(function ($cart) {
                    return $cart->price * $cart->quantity;
                }),
                'order_date' => now(),
                'order_status' => 'Processing',
                'createdAt' => now(),
            ]);
    
            // Clear the cart for the user
            Cart::where('user_id', $userId)->delete();
    
            // Send the order confirmation email to the user
            Mail::to($user->email)->send(new OrderConfirmationMail($order, $productsOrdered));
    
            // Send the order notification to all admins
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                Mail::to($admin->email)->send(new AdminOrderNotification($order));
            }

            if (!Auth::check()) {
                Auth::login($user);
                return redirect()->route('home')->with('success', 'Your order has been placed successfully and your account is already registered. Please note that it is subject to approval, and you will receive an email notification from us shortly.');
            } else {
                return redirect()->route('home')->with('success', 'Your order has been placed successfully. Please note that it is subject to approval, and you will receive an email notification from us shortly.');
            }
        }
    
        return redirect()->back()->with('error', 'Your cart is empty.');
    }      
}
