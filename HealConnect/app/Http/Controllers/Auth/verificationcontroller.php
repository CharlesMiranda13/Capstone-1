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
    public function show()
    {
        return view('auth.reg_verify');
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
            $user->verification_code = null; // clear code after verification
            $user->save();

            // Log the user in
            auth()->login($user);

            // Redirect dynamically based on role
            switch ($user->role) {
                case 'patient':
                    $route = 'patient.home';
                    break;
                case 'therapist':
                    $route = 'therapist.home';
                    break;
                case 'clinic':
                    $route = 'clinic.home';
                    break;
                default:
                    $route = 'home';
            }

            return redirect()->route($route)
                ->with('info', 'Your email is verified! Your account is still being verified by the admin.');
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
        $newCode = Str::upper(Str::random(6));

        $user->verification_code = $newCode;
        $user->save();

        // Send the code via email
        Mail::to($user->email)->send(new VerificationCodeMail($user));

        return back()->with('success', 'A new verification code has been sent to your email!');
    }
}
