<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function create()
    {
        // Fetch all therapists
        $therapists = User::whereIn('role', ['therapist', 'clinic'])->get();

        // Return the Blade file with data
        return view('user.patients.appointment', compact('therapists'));
    }

    // Handle appointment submission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_type' => 'required|string',
            'therapist_id' => 'required|exists:therapists,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

 
        $validated['patient_id'] = auth()->id();
        $validated['status'] = 'pending';

        Appointment::create($validated);

        return redirect()->route('patient.appointments.create')->with('success', 'Appointment request submitted successfully!');
    }
}
