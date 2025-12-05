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
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'region' => 'required|string|max:50',
            'postal_code' => 'nullable|digits:4',
            'phone' => 'required|string|max:20',
            'ValidIDFront' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ValidIDBack' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
        } 
        
        if ($type == 'clinic') {
            $rules['clinic_type'] = 'required|in:public,private';
        }
        
        if ($type == 'clinic' || $type == "therapist") {
            $rules['license'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            $rules['start_year'] = 'required|integer|min:1900|max:' . date('Y');
        }
        
        if ($type == 'therapist' || $type == 'clinic') {
            $rules['specialization'] = 'nullable|array';
            $rules['specialization.*'] = 'string|max:255'; 
        }

        $messages = [
            'email.unique' => 'The email has already been taken.',
            'password.confirmed' => 'The password confirmation does not match.',
            'ValidIDFront.mimes' => 'The Valid ID must be a file of type: jpg, jpeg, png, pdf.',
            'ValidIDBack.mimes' => 'The Valid ID must be a file of type: jpg, jpeg, png, pdf.',
            'license.mimes' => 'The License must be a file of type: jpg, jpeg, png, pdf.',
            'clinic_type.required' => 'Please select the clinic type.',
            'clinic_type.in' => 'Invalid clinic type selected.',
            'street.required' => 'Street address is required.',
            'barangay.required' => 'Barangay is required.',
            'city.required' => 'City/Municipality is required.',
            'province.required' => 'Province is required.',
            'region.required' => 'Region is required.',
            'postal_code.digits' => 'Postal code must be exactly 4 digits.',
        ];

        $request->validate($rules, $messages);

        // Generate verification code
        $verificationCode = Str::upper(Str::random(6));

        // Handle file uploads
        $front = $request->file('ValidIDFront')->store('valid_ids', 'public');
        $back  = $request->file('ValidIDBack')->store('valid_ids', 'public');

        $validIdPath = json_encode([
            'front' => $front,
            'back' => $back
        ]);

        $licensePath = ($type === 'clinic' || $type === 'therapist')
            ? $request->file('license')->store('licenses', 'public')
            : null;

        $startDate = ($type === 'clinic' || $type === 'therapist')
            ? $request->start_year 
            : null;

        // Combine address fields into a structured format
        $fullAddress = $this->formatAddress($request);

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
            'address' => $fullAddress, // Using formatted address for backward compatibility
            // Store individual address components
            'street' => $request->street,
            'barangay' => $request->barangay,
            'city' => $request->city,
            'province' => $request->province,
            'region' => $request->region,
            'postal_code' => $request->postal_code,
            'dob' => $request->dob ?? null,
            'gender' => $request->Gender ?? null,
            'specialization' => $request->has('specialization')
                ? implode(',', $request->specialization)
                : null,
            'start_year' => $startDate,
            'clinic_type' => $type === 'clinic' ? $request->clinic_type : null, 
            'plan'=> null,
            'subscription_status' => 'inactive',
        ]);

        // Send verification email
        Mail::to($user->email)->send(new VerificationCodeMail($user));

        // Redirect to verification page
        return redirect()->route('verification.notice')
                         ->with('email', $user->email)
                         ->with('info', 'A verification code has been sent to your gmail.');
    }

    /**
     * Format address components into a single string
     */
    private function formatAddress(Request $request)
    {
        $addressParts = [
            $request->street,
            $request->barangay,
            $request->city,
            $request->province,
            $request->region
        ];

        if ($request->filled('postal_code')) {
            $addressParts[] = $request->postal_code;
        }

        return implode(', ', array_filter($addressParts));
    }
}