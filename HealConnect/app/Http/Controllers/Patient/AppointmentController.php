<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // Show all appointments of the logged-in patient
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id())
            ->with('provider')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('user.patients.appointments', compact('appointments'));
    }

    // Show booking form for a specific therapist/clinic
    public function create($therapistId)
    {
        $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('id', $therapistId)
            ->firstOrFail();

        // Get service list
        $servicesList = $therapist->services
            ->pluck('appointment_type')
            ->map(fn($s) => explode(',', $s))
            ->flatten()
            ->all();

        // Get availabilities
        $availabilities = $therapist->availability()
            ->where('is_active', true)
            ->whereDate('date', '>=', now())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get(['date', 'day_of_week', 'start_time', 'end_time']);

        $dates = $availabilities->map(function ($availability) {
            return [
                'date' => $availability->date,
                'day_of_week' => $availability->day_of_week,
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
            ];
        });

        // GET BOOKED TIMES (for filtering)

        $bookedTimes = Appointment::where('provider_id', $therapistId)
            ->where('provider_type', get_class($therapist))
            ->whereDate('appointment_date', '>=', now())
            ->get()
            ->groupBy('appointment_date')
            ->map(fn($group) => $group->pluck('appointment_time')->toArray());

        return view('user.patients.appointment_booking', [
            'therapist' => $therapist,
            'servicesList' => $servicesList,
            'availabilities' => $dates,
            'bookedTimes' => $bookedTimes,
        ]);
    }

    // Store new appointment request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_type' => 'required|string',
            'therapist_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $therapist = User::findOrFail($validated['therapist_id']);
    
        // PREVENT DOUBLE BOOKING

        $isTaken = Appointment::where('provider_id', $therapist->id)
            ->where('provider_type', get_class($therapist))
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->exists();

        if ($isTaken) {
            return back()
                ->withInput()
                ->with('error', 'This time slot is already booked. Please choose another one.');
        }

        // CREATE APPOINTMENT

        Appointment::create([
            'appointment_type' => $validated['appointment_type'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'notes' => $validated['notes'] ?? null,
            'patient_id' => auth()->id(),
            'provider_id' => $therapist->id,
            'provider_type' => get_class($therapist),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('patient.appointments.index')
            ->with('success', 'Appointment request submitted successfully!');
    }
}
