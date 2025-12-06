<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    // Show user login form
    public function showLoginForm()
    {
        return view('.logandsign'); 
    }

    // Handle user login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user needs to be redirected to subscription
            if (session('redirect_to_subscription')) {
                $plan = session('redirect_to_subscription');
                
                // Clear sessions
                session()->forget(['redirect_to_subscription', 'selected_plan_for_registration']);
                
                // Only redirect therapists and clinics to subscription
                if ($user->role === 'therapist' || $user->role === 'clinic') {
                    return redirect()->route('subscribe.show', $plan);
                }
            }

            // Normal login redirects based on role
            switch ($user->role) {
                case 'patient':
                    return redirect()->route('patient.home');
                case 'therapist':
                    return redirect()->route('therapist.home');        
                case 'clinic':
                    return redirect()->route('clinic.home');
                default:
                    return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials!',
        ])->onlyInput('email');
    }

    // Handle user logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); 
    }
}