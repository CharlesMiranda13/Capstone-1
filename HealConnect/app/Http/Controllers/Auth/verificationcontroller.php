<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class VerificationController extends Controller
{
    // Show verification page
    public function show(Request $request)
    {
        $email = $request->session()->get('email'); // fixed semicolon

        if (!$email) {
            return redirect()->route('register.form', 'patient')
                             ->with('error', 'Please register first.');
        }

        return view('auth.reg_verify', compact('email'));
    }

    // Confirm verification code
    public function confirm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->where('verification_code', $request->verification_code)
                    ->first();

        if ($user) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->status = 'Pending'; // account pending admin approval
            $user->save();

            // Log user in
            auth()->login($user);

            // Redirect based on role with pending message
            $route = match($user->role) {
                'patient' => 'patient.home',
                'therapist' => 'therapist.home',
                'clinic' => 'clinic.home',
                default => 'home',
            };

            return redirect()->route($route)
                             ->with('info', 'Your email is verified! Account Status: Pending. Admin is verifying your application. Please wait for a number of business days.');
        }

        return back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }

    // Resend code
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $newCode = Str::upper(Str::random(6));

        $user->verification_code = $newCode;
        $user->save();

        Mail::to($user->email)->send(new VerificationCodeMail($user));

        return back()->with('success', 'A new verification code has been sent to your email!');
    }
}
