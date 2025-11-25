<?php

namespace App\Http\Controllers\Indtherapist;

use App\Http\Controllers\TherapistController\ptController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Availability;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IndtherapistController extends ptController
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $data = $this->getDashboardData();
        return view('user.therapist.independent.independent', $data);
    }

    /** ---------------- AVAILABILITY ---------------- */
    public function availability()
    {
        $user = Auth::user();

        $availabilities = Availability::with('appointments')
            ->where('provider_id', $user->id)
            ->orderBy('date', 'asc')
            ->simplePaginate(3);

        $calendarAvailabilities = Availability::where('provider_id', $user->id)->get();
        
        $existingPrice = \App\Models\TherapistService::where('serviceable_id', $user->id)
            ->where('serviceable_type', get_class($user))
            ->value('price');

        $existingServices = $this->getServices($user);

        return view('user.therapist.independent.services', compact(
            'user',
            'availabilities',
            'calendarAvailabilities',
            'existingServices',
            'existingPrice'
        ));
    }

    public function storeServices(Request $request)
    {
        $request->validate([
            'appointment_types' => 'required|array',
            'price' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $this->saveServices(
            $user,
            $request->input('appointment_types', []),
            $request->input('price') 
        );


        return back()->with('success', 'Your offered services have been updated successfully!');
    }

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

        return back()->with('success', 'Availability added successfully.');
    }

    public function destroy($id)
    {
        $this->destroyAvailabilityById($id, Auth::user());
        return back()->with('success', 'Availability deleted successfully.');
    }

    public function toggleAvailability($id)
    {
        $this->toggleAvailabilityById($id, Auth::user());
        return back()->with('success', 'Availability status updated successfully.');
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

        // keep unique patient listing if desired by view (original did this)
        $appointments = $appointments->unique('patient_id')->values();

        foreach ($appointments as $appointment) {
            $appointment->record_count = Appointment::where('provider_id', $appointment->provider_id)
                ->where('patient_id', $appointment->patient_id)
                ->where('status', 'completed')
                ->count();
        }

        return view('user.therapist.independent.appointment', compact('appointments'));
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        return parent::updateAppointmentStatus($request, $id);
    }

    /** ---------------- CLIENTS ---------------- */
    public function clients(Request $request)
    {
        $user = Auth::user();

        $patients = $this->getPatientsFor($user->id, User::class);

        if ($request->filled('search')) {
            $patients = $patients->filter(fn ($p) =>
                str_contains(strtolower($p->name), strtolower($request->search))
            );
        }

        if ($request->filled('gender')) {
            $patients = $patients->filter(fn ($p) => $p->gender === $request->gender);
        }

        return view('User.Therapist.client', compact('user', 'patients'));
    }

    /** ---------------- PROFILE ---------------- */
    public function profile()
    {
        $data = $this->getProfileData();
        return view('user.therapist.independent.profile', $data);

    }

    /** ---------------- PATIENT PROFILE ---------------- */
    public function patientProfile($id)
    {
        $therapist = Auth::user();

        $patient = User::where('role', 'patient')->findOrFail($id);

        $appointments = Appointment::where('patient_id', $patient->id)
            ->where('provider_id', $therapist->id)
            ->where('provider_type', User::class)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('user.therapist.patient_profile', compact('patient', 'appointments'));
    }
}
