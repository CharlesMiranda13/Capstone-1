<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Events\AppointmentUpdateEvent; 

class AppointmentController extends Controller
{
    /** ---------------- LIST APPOINTMENTS ---------------- */
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id())
            ->with('provider')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('user.patients.appointments', compact('appointments'));
    }

    /** ---------------- BOOKING FORM ---------------- */
    public function create($therapistId)
    {
        $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('id', $therapistId)
            ->firstOrFail();

        // Services
        $servicesList = $therapist->services
            ->pluck('appointment_type')
            ->map(fn($s) => explode(',', $s))
            ->flatten()
            ->all();

        // Availabilities
        $therapistAvailability = $therapist->availability()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereDate('date', '>', now()->toDateString()) // future dates
                ->orWhere(function ($q2) {
                    $q2->whereDate('date', now()->toDateString()) // today
                        ->whereTime('end_time', '>', now()->format('H:i:s')); // still upcoming
                });
            })
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get(['date', 'start_time', 'end_time'])
            ->map(function ($slot) {
                return [
                    'date' => $slot->date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                ];
            });
        // Booked times
        $bookedTimes = Appointment::where('provider_id', $therapistId)
            ->where('provider_type', get_class($therapist))
            ->whereDate('appointment_date', '>=', now())
            ->get()
            ->groupBy('appointment_date')
            ->map(fn($group) => $group->pluck('appointment_time')->toArray());

        return view('user.patients.appointment_booking', compact(
            'therapist',
            'servicesList',
            'therapistAvailability',
            'bookedTimes'
        ));
    }


    /** ---------------- STORE APPOINTMENT ---------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'therapist_id' => 'required|exists:users,id',
            'appointment_type' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $therapist = User::findOrFail($validated['therapist_id']);

        // Prevent double booking
        $isTaken = Appointment::where('provider_id', $therapist->id)
            ->where('provider_type', get_class($therapist))
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->exists();

        if ($isTaken) {
            return back()->withInput()->with('error', 'This time slot is already booked. Please choose another one.');
        }

        // Create appointment
        Appointment::create([
            'therapist_id' => $therapist->id,
            'provider_id' => $therapist->id,
            'provider_type' => get_class($therapist),
            'appointment_type' => $validated['appointment_type'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'notes' => $validated['notes'] ?? null,
            'patient_id' => auth()->id(),
            'status' => 'pending',
        ]);

        // Notify the therapist about new appointment request
        $this->broadcastAppointmentNotification($therapist->id);

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment request submitted successfully!');
    }

    /**
     * Broadcast appointment notification to a user
     */

    private function broadcastAppointmentNotification($userId)
    {
        $appointmentCount = Appointment::where('provider_id', $userId)
            ->where('status', 'pending')
            ->whereDate('appointment_date', '>=', now())
            ->count();
        broadcast(new AppointmentUpdateEvent($userId, $appointmentCount));
    }
}