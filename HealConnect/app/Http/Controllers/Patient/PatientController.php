<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\Record;
use App\Models\Referral;
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
            ->orderBy('appointment_date', 'asc')
            ->take(3)
            ->get();

        $therapists = $this->getTherapists();

        return view('user.patients.patient', compact('user', 'appointments', 'therapists'));
    }

    // List of Therapists (for logged-in patients)
    public function listOfTherapist(Request $request)
    {
        $query = \App\Models\User::whereIn('role', ['therapist', 'clinic'])
            ->where('is_verified_by_admin', true)
            ->with('availability')
            ->with('activeAvailability');

        if ($request->filled('category')) {
            if ($request->category == 'independent') {
                $query->where('role', 'therapist');
            } elseif ($request->category == 'clinic') {
                $query->where('role', 'clinic');
        }

        if ($request->filled('service')){
            $query->where('service_type', $request->service);
        }
    }

            $therapists = $query->paginate(10);
            $patientHasApprovedReferral = Referral::where('patient_id', Auth::id())
                ->where('status', 'approved')
                ->exists();

            return view('user.patients.listoftherapist', compact('therapists','patientHasApprovedReferral'));
    }
    public function showProfile($id)
    {
        $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('is_verified_by_admin', true)
            ->with(['availability'])
            ->findOrFail($id);

        return view('user.patients.therapist_profile', compact('therapist'));
    }



    // Public Therapist List (for non-logged-in users)
    public function publicTherapists(Request $request)
    {
        $query = User::whereIn('role', ['therapist', 'clinic'])
            ->where('is_verified_by_admin', true)
            ->with('availability')
            ->with('activeAvailability');

        if ($request->filled('category')) {
            if ($request->category == 'independent') {
                $query->where('role', 'therapist');
            } elseif ($request->category == 'clinic') {
                $query->where('role', 'clinic');
            }
        }

        $therapists = $query->paginate(10);

        return view('ptlist', compact('therapists'));
    }

    public function publicTherapistProfile($id)
{
    $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('is_verified_by_admin', true)
            ->with(['availability'])
            ->findOrFail($id);

        return view('view_profile', compact('therapist'));
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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
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
