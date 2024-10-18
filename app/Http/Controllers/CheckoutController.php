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
            // Validation rules for unauthenticated users
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'store_name' => 'required|string|max:255',
                'store_address' => 'required|string|max:255',
                'store_phone_number' => 'required|string|max:20',
                'store_email' => 'required|email|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
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
                'store_email' => 'required|email|max:255',
            ]);
        }
        
        // Check if user is authenticated
        $user = Auth::user();
        
        // Use user ID if authenticated, otherwise use session ID
        $userId = $user ? $user->id : session()->getId(); // Use session ID if not logged in
    
        // Log the user ID being used
        \Log::info('Attempting to retrieve cart', ['user_id' => $userId]);
    
        // Retrieve the cart items for the user or session
        $carts = Cart::where('user_id', $userId)->get();
    
        // Log the retrieved carts
        \Log::info('Cart Retrieval', [
            'user_id' => $userId,
            'carts_count' => $carts->count(),
        ]);
    
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
                    'store_email' => $request->store_email,
                ]);
                $storeId = $existingStore->id; // Get the existing store ID
            } else {
                // Create new store information
                $newStore = Store::create([
                    'store_name' => $request->store_name,
                    'store_owner' => $user->id,
                    'store_address' => $request->store_address,
                    'store_phone_number' => $request->store_phone_number,
                    'store_email' => $request->store_email,
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
