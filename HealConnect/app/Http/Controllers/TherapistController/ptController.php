<?php

namespace App\Http\Controllers\TherapistController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
                return $appointmentDateTime->greaterThan($now);
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

    /**
     * Patient medical records view
     */
    public function patient_records($patientId)
    {
        $patient = User::findOrFail($patientId);

        return view('User.Therapist.patients_records', [
            'patient' => $patient,
            'ehr' => $patient->ehr,
            'therapies' => $patient->therapies,
            'exercises' => $patient->exercises,
        ]);
    }

    /**
     *  Update Electronic Health Record (EHR)
     */
    public function updateEHR(Request $request, $patientId)
    {
        $request->validate([
            'ehr' => 'required|string',
        ]);

        $patient = User::findOrFail($patientId);
        $patient->ehr = $request->ehr;
        $patient->save();

        return redirect()->back()->with('success', 'EHR updated successfully!');
    }

    /**
     *  Update Treatment Plan
     */
    public function updateTreatment(Request $request, $patientId)
    {
        $request->validate([
            'treatment_plan' => 'required|string',
        ]);

        $patient = User::findOrFail($patientId);
        $patient->therapies = $request->treatment_plan;
        $patient->save();

        return redirect()->back()->with('success', 'Treatment plan updated successfully!');
    }

    /**
     *  Update Progress Notes
     */
    public function updateProgress(Request $request, $patientId)
    {
        $request->validate([
            'progress_note' => 'required|string',
        ]);

        $patient = User::findOrFail($patientId);
        $patient->exercises = $request->progress_note;
        $patient->save();

        return redirect()->back()->with('success', 'Progress note updated successfully!');
    }
}
