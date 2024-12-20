<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Promos;
use App\Models\Order;

use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user(); // Get the authenticated user
    
            // Check if the user has any delivered orders
            $hasDeliveredOrders = Order::where('user_id', $user->id)
                ->where('order_status', 'Delivered')
                ->exists();
    
            // Redirect based on the order check
            if ($hasDeliveredOrders) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('home');; // Redirect to shop if no delivered orders exist
            }
        }
    
        $promos = Promos::all(); // Retrieve all promos
    
        return view('pages.login', compact('promos')); // Show the login form if not authenticated
    }
    

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Get the authenticated user
    
            // Check if the user is verified
            if (!$user->verified) {
                Auth::logout(); // Log out the user if not verified
                return back()->withErrors([
                    'email' => 'Account not verified yet. Please go to your email and verify the account.',
                ]);
            }
    
            // Check if the user role is 'admin'
            if ($user->role === 'admin') {
                Auth::logout();
                return redirect()->to('https://admin.ukayukaysupplier.com/login'); // Redirect admin to admin login
            }
    
            // Check if the user has any delivered orders
            $hasDeliveredOrders = Order::where('user_id', $user->id)
                ->where('order_status', 'Delivered')
                ->exists();
    
            // Redirect based on the order check
            if ($hasDeliveredOrders) {
                return redirect()->intended('/dashboard'); // Redirect to dashboard if delivered orders exist
            } else {
                return redirect('/'); // Redirect to shop if no delivered orders exist
            }
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }      

    public function showRegisterForm()
    {
        if (Auth::check()) {
            $user = Auth::user(); // Get the authenticated user
    
            // Check if the user has any delivered orders
            $hasDeliveredOrders = Order::where('user_id', $user->id)
                ->where('order_status', 'Delivered')
                ->exists();
    
            // Redirect based on the order check
            if ($hasDeliveredOrders) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('home'); // Redirect to shop if no delivered orders exist
            }
        }

        return view('pages.register'); // Show the login form if not authenticated
    }

    public function register(Request $request)
    {
        // Validate the registration data
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // Create a new user instance
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash the password
            'role' => 'user', // Default role
            'verified' => true, // Set verification status to true
        ]);

        // Log the user in
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful! You are now logged in.');
    }


    /*public function register(Request $request)
    {
        // Validate the registration data
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8', // Removed confirmation requirement
        ]);
    
        // Create a new user instance
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash the password
            'role' => 'user', // Default role
            'verified' => false, // Default verification status
            'verification_token' => Str::random(32), // Generate a random token
        ]);
    
        Mail::to($user->email)->send(new VerificationEmail($user));
    
        return redirect()->route('login')->with('success', 'You will receive an email to verify your account.');
    }*/
    
    public function verify(Request $request, $token)
    {
        // Find the user with the given verification token
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid verification token.');
        }

        // Update the user's verified status and clear the verification token
        $user->verified = true;
        $user->verification_token = null; // Clear the token after verification
        $user->save();

        return redirect()->route('login')->with('success', 'Your email has been verified successfully. You can now log in.');
    }
}
