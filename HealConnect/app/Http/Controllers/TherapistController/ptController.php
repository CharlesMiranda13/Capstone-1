<?php

namespace App\Http\Controllers\TherapistController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Availability;
use Carbon\Carbon;
use App\Mail\AppointmentStatusMail;

class ptController extends Controller
{
    /**
     * Determine provider type based on logged-in user's role.
     */
    protected function getProviderQuery(): array
    {
        $user = Auth::user();

        $providerType = $user->role === 'clinic'
            ? \App\Models\Clinic::class
            : \App\Models\User::class;

        return [
            'provider_id' => $user->id,
            'provider_type' => $providerType
        ];
    }

    /**
     * Shared dashboard data for all therapists (clinic or independent)
     */
    protected function getDashboardData()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $provider = $this->getProviderQuery();

        // Upcoming appointments (next 3)
        $appointments = Appointment::where($provider)
            ->with('patient')
            ->get()
            ->filter(fn($a) => Carbon::parse($a->appointment_date . ' ' . $a->appointment_time)->greaterThan($now))
            ->sortBy('appointment_date')
            ->take(3);

        // Total clients
        $appointmentCount = Appointment::where($provider)
            ->distinct('patient_id')
            ->count('patient_id');

        // Completed sessions
        $completedSessions = Appointment::where($provider)
            ->where('status', 'completed')
            ->count();

        // Cancellations
        $cancellations = Appointment::where($provider)
            ->where('status', 'cancelled')
            ->count();

        // Monthly appointments data (for line chart)
        $daysInMonth = $now->daysInMonth;
        $monthlyData = collect(range(1, $daysInMonth))->map(fn($d) =>
            Appointment::where($provider)
                ->whereDate('appointment_date', $now->startOfMonth()->addDays($d - 1))
                ->count()
        );

        // Appointments by type (for doughnut chart)
        $appointmentTypes = Appointment::where($provider)
            ->selectRaw('appointment_type, COUNT(*) as count')
            ->groupBy('appointment_type')
            ->pluck('count', 'appointment_type');

        return [
            'user' => $user,
            'appointments' => $appointments,
            'appointmentCount' => $appointmentCount,
            'completedSessions' => $completedSessions,
            'cancellations' => $cancellations,
            'monthlyData' => $monthlyData,
            'appointmentTypes' => $appointmentTypes,
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

        // restrict access to only patients under this provider
        $provider = $this->getProviderQuery();
        $isLinked = Appointment::where($provider)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$isLinked) {
            abort(403, 'Unauthorized access to this patient.');
        }

        return view('User.Therapist.patients_records', [
            'patient' => $patient,
            'ehr' => $patient->ehr ?? null,
            'therapies' => $patient->therapies ?? null,
            'exercises' => $patient->exercises ?? null,
        ]);
    }

    /**
     * Update Electronic Health Record (EHR)
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
     * Update Treatment Plan
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
     * Update Progress Notes
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

    /**
     * Update appointment status (approved, rejected, completed)
     * Sends email notification to patient.
     */
    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->status = $request->status;
        $appointment->save();

        // Update related availability
        $availability = Availability::where('provider_id', $appointment->provider_id)
            ->whereDate('date', $appointment->appointment_date)
            ->first();

        if ($availability) {
            if ($request->status === 'completed') {
                $availability->status = 'completed';
                $availability->is_active = false;
            } elseif (in_array($request->status, ['approved', 'pending'])) {
                $availability->status = 'active';
                $availability->is_active = true;
            } elseif ($request->status === 'rejected') {
                $availability->status = 'cancelled';
                $availability->is_active = false;
            }
            $availability->save();
        }

        // Send email notification to patient
        try {
            Mail::to($appointment->patient->email)
                ->send(new AppointmentStatusMail($appointment));
        } catch (\Exception $e) {
            \Log::error("Failed to send appointment email: " . $e->getMessage());
        }

        return back()->with('success', 'Appointment status updated and patient notified!');
    }
}
