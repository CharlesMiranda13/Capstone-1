<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Notification;

class TherapistController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $appointments = Appointment::where('therapist_id', $user->id)
            ->with('patient') // relationship in Appointment model
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        // $notifications = Notification::where('user_id', $user->id)
        //     ->latest()
        //     ->take(5)
        //     ->get();

        return view('user.therapists.dashboard', compact('user', 'appointments'));
    }
}
