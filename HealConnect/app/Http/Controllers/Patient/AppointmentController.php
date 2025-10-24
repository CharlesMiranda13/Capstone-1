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
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id())->get();
        return view('user.patients.appointments', compact('appointments'));
    }

    public function create($therapistId)
    {
        $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('id', $therapistId)
            ->firstOrFail();

        $services = DB::table('therapist_services')
            ->where('therapist_id', $therapist->id)
            ->value('appointment_type');

        $servicesList = $services ? explode(',', $services) : [];

    
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

        return view('user.patients.appointment_booking', ['therapist' => $therapist,'servicesList' => $servicesList,'availabilities' => $dates, ]);

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
