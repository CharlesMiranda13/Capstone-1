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
        // Base validation for all users
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'ValidID' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Type-based additional validation
        if ($type == 'patient' || $type == 'therapist') {
            $rules = array_merge($rules, [
                'Fname' => 'required|string|max:255',
                'Mname' => 'nullable|string|max:255',
                'Lname' => 'required|string|max:255',
                'dob' => 'required|date',
                'Gender' => 'required|string',
            ]);
        } elseif ($type == 'clinic' || $type == "therapist") {
            $rules = array_merge($rules, [
                'Fname' => 'required|string|max:255', 
                'license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);
        }
        if ($type == 'therapist' || $type == 'clinic') {
            $rules['specialization'] = 'nullable|array';
            $rules['specialization.*'] = 'string|max:255'; 
        }

        $messages = [
            'email.unique' => 'The email has already been taken.',
            'password.confirmed' => 'The password confirmation does not match.',
            'ValidID.mimes' => 'The Valid ID must be a file of type: jpg, jpeg, png, pdf.',
            'License.mimes' => 'The License must be a file of type: jpg, jpeg, png, pdf.',
        ];

        $request->validate($rules, $messages);

        // Generate verification code
        $verificationCode = Str::upper(Str::random(6));

        // Handle file uploads
        $validIdPath = $request->file('ValidID')->store('valid_ids', 'public');
        $licensePath = ($type === 'clinic' || $type === 'therapist')
            ? $request->file('license')->store('licenses', 'public')
            : null;

        // Create user
        $user = User::create([
            'name' => $type === 'clinic'
                ? $request->Fname
                : $request->Fname . ' ' . $request->Mname . ' ' . $request->Lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $type, // patient, therapist, or clinic
            'verification_code' => $verificationCode,
            'valid_id_path' => $validIdPath,
            'license_path' => $licensePath,
            'status' => 'Pending',
            'phone' => $request->phone,
            'address' => $request->address,
            'dob' => $request->dob,
            'gender' => $request->Gender,
            'specialization' => $request->has('specialization')
                ? implode(',', $request->specialization)
                : null,
            'experience_years'=> $request->experience_years
        ]);

        // Send verification email
        Mail::to($user->email)->send(new VerificationCodeMail($user));

        // Redirect to verification page
        return redirect()->route('verification.notice')
                         ->with('email', $user->email)
                         ->with('info', 'A verification code has been sent to your gmail.');
    }
}
