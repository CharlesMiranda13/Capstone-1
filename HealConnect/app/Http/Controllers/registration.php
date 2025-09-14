<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    // Show the form
    public function showRegistrationForm()
    {
        return view('register.patientreg');
    }

    // Handle registration
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'patient',
            'verification_code' => rand(100000, 999999),
        ]);

        Log::info("Verification code for {$user->email}: {$user->verification_code}");

        return $user;
    }

    // You’ll also need a method to handle the POST request
    public function register(Request $request)
    {
        $data = $request->all();

        $user = $this->create($data);

        return redirect()->route('verification.show', ['email' => $user->email]);
    }
}
