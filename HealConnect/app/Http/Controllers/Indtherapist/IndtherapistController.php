<?php

namespace App\Http\Controllers\Indtherapist;

use App\Http\Controllers\TherapistController\ptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Availability;
use App\Models\TherapistService;
use Carbon\Carbon;

class IndtherapistController extends ptController
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $data = $this->getDashboardData();
        return view('user.therapist.independent.independent', $data);
    }

    /** ---------------- PROFILE ---------------- */
    public function profile()
    {
        $data = $this->getProfileData();
        return view('user.therapist.independent.profile', $data);
    }

    /** ---------------- SERVICES ---------------- */
    public function services()
    {
        $therapist = Auth::user();

        $existingServices = $this->getServices($therapist);

        $existingPrice = TherapistService::where('serviceable_id', $therapist->id)
            ->where('serviceable_type', get_class($therapist))
            ->value('price') ?? '';

        $availabilities = Availability::where('provider_id', $therapist->id)
            ->where('provider_type', get_class($therapist))
            ->orderBy('date')
            ->paginate(10);

        $calendarAvailabilitiesQuery = Availability::where('provider_id', $therapist->id)
            ->where('provider_type', get_class($therapist))
            ->orderBy('date')
            ->get();

        $calendarAvailabilities = $this->getCalendarSchedules($calendarAvailabilitiesQuery);

        return view('user.therapist.independent.services', compact(
            'therapist',
            'existingServices',
            'existingPrice',
            'availabilities',           
            'calendarAvailabilities'     
        ));
    }
    public function storeServices(Request $request)
    {
        $therapist = Auth::user();

        $request->validate([
            'appointment_types' => 'required|array',
            'price' => 'nullable|string|max:50',
        ]);
        $appointmentTypes = $request->appointment_types;

        if (!is_array($appointmentTypes)) {
            $appointmentTypes = json_decode($appointmentTypes, true);
        }

        $this->saveServices($therapist, $appointmentTypes, $request->price);

        return back()->with('success', 'Appointment types updated!');
    }

    /** ---------------- AVAILABILITY / SCHEDULE ---------------- */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $dayOfWeek = Carbon::parse($formattedDate)->format('l');

        Availability::create([
            'provider_id' => Auth::id(),
            'provider_type' => User::class,
            'date' => $formattedDate,
            'day_of_week' => $dayOfWeek,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Availability added successfully!');
    }

    public function toggleAvailability($id)
    {
        $therapist = Auth::user();
        $this->toggleAvailabilityById($id, $therapist);

        return back()->with('success', 'Schedule updated successfully!');
    }

    public function destroy($id)
    {
        $therapist = Auth::user();
        $this->destroyAvailabilityById($id, $therapist);

        return back()->with('success', 'Schedule deleted successfully!');
    }

    /** ---------------- CLIENTS ---------------- */
    public function clients(Request $request)
    {
        $therapist = Auth::user();

        $patients = $this->getPatientsFor($therapist->id, User::class);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $patients = $patients->filter(fn ($p) => str_contains(strtolower($p->name), $search));
        }

        if ($request->filled('gender')) {
            $patients = $patients->filter(fn ($p) => $p->gender === $request->gender);
        }

        return view('user.therapist.client', compact('therapist', 'patients'));
    }

    /** ---------------- APPOINTMENTS ---------------- */
    public function appointments(Request $request)
    {
        $user = Auth::user();

        $query = Appointment::where('provider_id', $user->id)
            ->where('provider_type', User::class)
            ->with('patient');

        $query = $this->applyAppointmentFilters($query, $request);

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        // Get unique patients (latest appointment for each patient)
        $appointments = $appointments->unique('patient_id')->values();

        // Add record count for each patient
        foreach ($appointments as $appointment) {
            $appointment->record_count = Appointment::where('provider_id', $appointment->provider_id)
                ->where('patient_id', $appointment->patient_id)
                ->where('status', 'completed')
                ->count();
        }

        return view('user.therapist.independent.appointment', compact('appointments'));
    }

}