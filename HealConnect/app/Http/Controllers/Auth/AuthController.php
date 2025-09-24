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
            $credentials['email'] = strtolower($credentials['email']); 

            if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            return redirect()->intended(route('admin.dashboard'));
            }

            if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
                $user = Auth::guard('web')->user();

                if ($user->role === 'therapist') {
                    return redirect()->intended(route('therapist.home'));
                } elseif ($user->role === 'clinic') {
                    return redirect()->intended(route('clinic.home'));
                } else {
                    return redirect()->intended(route('patient.home'));
                }
            }

            // If both fail
            return back()->withErrors([
                'email' => 'Invalid credentials!',
            ])->withInput();
    }


    public function dashboard()
    {
        return view('user.admin.admin');
    }
}
