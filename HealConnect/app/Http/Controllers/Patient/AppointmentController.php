<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id())->get();
        return view('user.patients.appointments', compact('appointments'));
    }

    public function create($therapistId)
    {
    // Get the therapist
        $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('id', $therapistId)
            ->firstOrFail();

    // Get therapist services from the table
        $services = DB::table('therapist_services')
            ->where('therapist_id', $therapist->id)
            ->value('appointment_type');

        $servicesList = $services ? explode(',', $services) : [];

    // Get active availabilities
        $availabilities = $therapist->availability()
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('user.patients.appointment_booking',compact('therapist', 'servicesList', 'availabilities')
    );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_type' => 'required|string',
            'therapist_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $validated['patient_id'] = auth()->id();
        $validated['status'] = 'pending';

        Appointment::create($validated);

        return redirect()
            ->route('patient.appointments.index')
            ->with('success', 'Appointment request submitted successfully!');
    }
}
