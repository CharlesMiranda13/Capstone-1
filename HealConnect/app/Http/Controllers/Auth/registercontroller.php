<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class RegisterController extends Controller
{
    public function showRegistrationForm($type)
    {
        switch ($type) {
            case 'therapist':
                return view('register.indtherapist');
            case 'clinic':
                return view('register.clinicreg');
            case 'patient':
                return view('register.patientreg');
            default:
                abort(404);
        }
    }

    public function register(Request $request, $type)
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

        // Generate verification code
        $verificationCode = Str::upper(Str::random(6));

        // Handle Valid ID upload
        $validIdPath = $request->file('ValidID')->store('valid_ids', 'public');

        // Create user
        $user = User::create([
            'name' => $request->Fname . ' ' . $request->Mname . ' ' . $request->Lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $type, // patient, therapist, or clinic
            'verification_code' => $verificationCode,
            'valid_id_path' => $validIdPath,
            'plan' =>$plan,
        ]);

        // Send verification email
        Mail::to($user->email)->send(new VerificationCodeMail($user));

        // Redirect to verification page with messages
        return redirect()->route('verification.notice')->with('email', $user->email)
                         ->with('info', 'A verification code has been sent to your email. Your account is still being verified by the admin.');
    }
}
