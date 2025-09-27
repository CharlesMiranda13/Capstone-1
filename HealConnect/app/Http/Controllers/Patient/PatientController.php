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
    public function dashboard()
    {
        $user = Auth::user();

        $appointments = Appointment::where('patient_id', $user->id)
            ->with('therapist')
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        //$records = Record::where('patient_id', $user->id)
            //->latest()
            //->take(3)
            //->get();

       // $notifications = Notification::where('user_id', $user->id)
         //   ->latest()
         //   ->take(5)
         //   ->get();

         return view('user.patients.patient', compact('user', 'appointments'));


        //return view('user.patients.patient', compact('user', 'appointments', 'records', 'notifications'));
    }

    public function listOfTherapist()
    {
        $therapists = User::whereIn('role', [User::ROLE_THERAPIST, User::ROLE_CLINIC])->get();

        return view('user.patients.listoftherapist', compact('therapists'));
    }
}