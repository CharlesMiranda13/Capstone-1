<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function listOfTherapist()
    {
        $therapists = $this->getTherapists();

        return view('user.patients.listoftherapist', compact('therapists'));
    }

    public function publicTherapists()
    {
        $therapists = User::verifiedTherapists()->get(); 
        return view('ptlist', compact('therapists')); 
    }

    public function settings()
    {
        $user = Auth::user();
        return view('user.patients.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update profile picture
        if ($request->hasFile('profile_picture')) {
            $fileName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads/profile_pictures'), $fileName);
            $user->profile_picture = $fileName;
        }

        // Update other details
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        // Update password if filled
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}



