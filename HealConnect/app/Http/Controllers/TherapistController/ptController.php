<?php

namespace App\Http\Controllers\TherapistController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Carbon\Carbon;

class ptController extends Controller
{
    /**
     * Shared dashboard data for all therapists (clinic or independent)
     */

    protected function getDashboardData()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $appointments = Appointment::where('provider_id', $user->id)
            ->with('patient')
            ->get()
            ->filter(function ($appointment) use ($now) {
                $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
                return $appointmentDateTime->greaterThan($now); // only future appointments
            })
            ->sortBy('appointment_date')
            ->take(3);

        $appointmentCount = Appointment::where('provider_id', $user->id)
            ->distinct('patient_id')
            ->count('patient_id');

        return [
            'user' => $user,
            'appointments' => $appointments,
            'appointmentCount' => $appointmentCount,
        ];
    }


    /**
     * Shared settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('shared.settings', compact('user'));
    }
}
