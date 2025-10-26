<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.logandsign');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('web')->user()->fresh();

            // Check admin verification + status
            if (!$user->is_verified_by_admin || $user->status !== 'Active') {
                Auth::logout();
                return redirect()->route('account.pending');
            }

            // Redirect based on role
            switch ($user->role) {
                case 'patient':
                    return redirect()->route('patient.home');
                case 'clinic':
                    return redirect()->route('clinic.home');
                case 'therapist':
                    return redirect()->route('therapist.home');
                default:
                    Auth::logout();
                    return redirect('/')->withErrors(['role' => 'Unauthorized role.']);
            }
        }

        return back()->withErrors([
            'email' => 'Invalid login details.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
