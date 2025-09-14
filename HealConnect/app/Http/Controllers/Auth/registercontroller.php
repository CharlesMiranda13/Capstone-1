<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    // Show patient registration form
    public function showRegistrationForm()
    {
        return view('register.patientreg');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'Fname' => 'required|string|max:255',
            'Mname' => 'nullable|string|max:255',
            'Lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'Gender' => 'required|string',
            'ValidID' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Generate random verification code
        $verificationCode = Str::upper(Str::random(6));

        // Create new user
        $user = User::create([
            'name' => $request->Fname . ' ' . $request->Mname . ' ' . $request->Lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'patient',
            'verification_code' => $verificationCode,
        ]);

        // ⚡ TODO: Send email here later

        // Redirect to verification page with email
        return redirect()->route('verification.notice')->with('email', $user->email);
    }
}
