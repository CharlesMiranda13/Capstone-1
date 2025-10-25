<?php

namespace App\Http\Controllers\Clinictherapist;

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
use App\Models\Availability;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class clinicController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $appointments = Appointment::where('clinic_id', $user->id)
            ->with('patient') // relationship in Appointment model
            ->orderBy('appointment_date', 'asc')
            ->take(3)
            ->get();
        $appointmentCount = Appointment::where('clinic_id', $user->id)
            ->distinct('patient_id')
            ->count('patient_id');
              
        return view('user.therapist.clinic.clinic', compact('user', 'appointments', 'appointmentCount'));

    }

}