<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.adminlogin');
    }

    // Handle login submission
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['email'] = strtolower($credentials['email']); // normalize email

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'therapist') {
                return redirect()->intended(route('therapist.dashboard'));
            } else {
                return redirect()->intended(route('patient.dashboard'));
            }
        }

        // If login fails
        return back()->withErrors([
            'email' => 'Invalid credentials!',
        ])->withInput();
    }

    // Show dashboard (example for admin)
    public function dashboard()
    {
        return view('user.admin.admin');
    }
}
