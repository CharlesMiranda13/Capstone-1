<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\Record;
use App\Models\Notification;
use App\Models\User;

class PatientController extends Controller
{
    private function getTherapists()
    {
        return User::verifiedTherapists()->get();
    }

    // Patient Dashboard
    public function dashboard()
    {
        $user = Auth::user();

        $appointments = Appointment::where('patient_id', $user->id)
            ->with('therapist')
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        $therapists = $this->getTherapists();

        return view('user.patients.patient', compact('user', 'appointments', 'therapists'));
    }

    // List of Therapists (inside patient view)
    public function listOfTherapist()
    {
        $therapists = \App\Models\User::whereIn('role', ['therapist', 'clinic'])
            ->where('is_verified_by_admin', true)
            ->get();
        return view('user.patients.listoftherapist', compact('therapists'));
    }

    // Public Therapist List (for non-logged in users)
    public function publicTherapists()
    {
        $therapists = User::verifiedTherapists()->get(); 
        return view('ptlist', compact('therapists')); 
    }

    // Settings Page
    public function settings()
    {
        $user = Auth::user();
        return view('user.patients.settings', compact('user'));
    }

    // Update Settings (Profile + Info + Password)
    public function updateProfile(Request $request) {
        $user = auth()->user();

        $request->validate([
            'Fname' => 'required|string|max:255',
            'Mname' => 'nullable|string|max:255',
            'Lname' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->Fname = $request->Fname;
        $user->Mname = $request->Mname;
        $user->Lname = $request->Lname;

        if ($request->hasFile('profile_picture')) {
            $fileName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads/profile_pictures'), $fileName);
            $user->profile_picture = $fileName;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateInfo(Request $request) {
        $user = auth()->user();

        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'dob' => 'required|date',
            'Gender' => 'required|string|in:male,female',
        ]);

        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->gender = $request->Gender;

        $user->save();

        return back()->with('success', 'Info updated successfully!');
    }

    public function updatePassword(Request $request) {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
        }
}
