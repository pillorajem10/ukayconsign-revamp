<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/'); // Redirect to the dashboard if authenticated
        }

        return view('pages.login'); // Show the login form if not authenticated
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Get the authenticated user
    
            // Check if the user role is 'admin'
            if ($user->role === 'admin') {
                return redirect()->to('https://admin.ukayukaysupplier.com/login'); // Redirect admin to admin login
            }
    
            return redirect()->intended('/'); // Redirect other users to the dashboard
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    
}
