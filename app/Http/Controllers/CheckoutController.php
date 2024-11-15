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
        // Validate request data based on authentication status
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
                'store_fb_link' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|min:8',
                'estimated_items_sold_per_month' => 'nullable|integer|min:0',
                'fb_link' => 'nullable|string|max:255',
                'government_id_card' => 'required|file|mimes:jpg,png,pdf|max:5048',
                'proof_of_billing' => 'required|file|mimes:jpg,png,pdf|max:5048',
                'selfie_uploaded' => 'required|file|mimes:jpg,png|max:5048',
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
                'store_fb_link' => 'required|string|max:255',
            ]);
        }
    
        // Check if user is authenticated
        $user = Auth::user();
        $userId = $user ? $user->id : session()->getId(); // Use session ID if not logged in
    
        // Retrieve cart items for the user or session
        $carts = Cart::where('user_id', $userId)->get();
    
        // Check if cart is not empty
        if ($carts->isNotEmpty()) {
            // Retrieve user's badge and set the order limit
            $badge = $user ? $user->badge : null;

            // If badge is not set, null, or undefined, assign "Silver" as the default
            if (!$badge) {
                $badge = 'Silver';
            }

            $limit = 0;
    
            // Set limit based on user badge
            switch ($badge) {
                case 'Silver':
                    $limit = 50000;
                    break;
                case 'Gold':
                    $limit = 75000;
                    break;
                case 'Platinum':
                    $limit = 100000;
                    break;
                default:
                    $limit = 50000; // Default limit for users with no badge or unknown badge
            }
    
            // Calculate the total price of the cart
            $totalPrice = $carts->sum(function ($cart) {
                return $cart->price * $cart->quantity;
            });
    
            // Check if the total price exceeds the user's limit
            if ($totalPrice > $limit && $limit > 0) {
                return redirect()->back()->with('error', "Sorry, but your badge is limited to consign orders below ₱" . number_format($limit, 0, '.', ',') . " worth. You can adjust the quantities in the cart page.");
            }

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
                // Update user's first and last names if they are not already set
                $user->update([
                    'fname' => $user->fname ?? $request->first_name,
                    'lname' => $user->lname ?? $request->last_name,
                ]);
            }
    
    
            // Check if the store exists
            $existingStore = Store::where('store_name', $request->store_name)->first();
    
            if ($existingStore) {
                // Update existing store information
                $existingStore->update([
                    'store_address' => $request->store_address,
                    'store_phone_number' => $request->store_phone_number,
                    'store_fb_link' => $request->store_fb_link,
                ]);
                $storeId = $existingStore->id;
            } else {
                // Create new store
                $newStore = Store::create([
                    'store_name' => $request->store_name,
                    'store_owner' => $user->id,
                    'store_address' => $request->store_address,
                    'store_fb_link' => $request->store_fb_link,
                    'store_phone_number' => $request->store_phone_number,
                    'store_total_earnings' => 0,
                    'store_status' => 'active',
                ]);
                $storeId = $newStore->id;
            }
    
            // Build the productsOrdered array
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
                'user_id' => $user->id,
                'products_ordered' => json_encode($productsOrdered),
                'address' => $request->address,
                'store_name' => $request->store_name,
                'email' => $user->email,
                'total_price' => $totalPrice,
                'order_date' => now(),
                'order_status' => 'Processing',
                'createdAt' => now(),
            ]);
    
            // Clear the cart for the user
            Cart::where('user_id', $userId)->delete();
    
            // Send order confirmation email to user
            Mail::to($user->email)->send(new OrderConfirmationMail($order, $productsOrdered));
    
            // Send order notification to admins
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                Mail::to($admin->email)->send(new AdminOrderNotification($order));
            }
    
            // Login the user if not authenticated
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
