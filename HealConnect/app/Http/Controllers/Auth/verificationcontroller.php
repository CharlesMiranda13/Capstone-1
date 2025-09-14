<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
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
            $user->verification_code = null; // clear code after verification
            $user->save();

            return redirect()->route('patient.home')->with('success', 'Your account has been verified!');
        }

        return back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }

    // Resend new code
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $newCode = strtoupper(\Illuminate\Support\Str::random(6));

        $user->verification_code = $newCode;
        $user->save();

        // ⚡ TODO: Send email here later

        return back()->with('success', 'A new verification code has been sent!');
    }
}
