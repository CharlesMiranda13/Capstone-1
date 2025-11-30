<?php

namespace App\Http\Controllers\Clinictherapist;

use App\Http\Controllers\TherapistController\ptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Availability;
use App\Models\TherapistService;
use Carbon\Carbon;

class ClinicController extends ptController
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $clinic = Auth::user();

        // Counts
        $totalTherapists = User::where('clinic_id', $clinic->id)
            ->where('role', 'therapist')->count();

        $totalEmployees = User::where('clinic_id', $clinic->id)
            ->where('role', 'employee')->count();

        // Total unique patients across clinic therapists
        $therapistIds = User::where('clinic_id', $clinic->id)
            ->where('role', 'therapist')
            ->pluck('id')
            ->toArray();

        $totalPatients = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->with('patient')
            ->get()
            ->pluck('patient')
            ->unique('id')
            ->count();

        $totalAppointments = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->count();

        $pendingAppointments = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->where('status', 'pending')
            ->count();

        // Per-therapist appointment totals (exclude cancelled)
        $therapists = User::where('clinic_id', $clinic->id)
            ->where('role', 'therapist')
            ->get();

        $therapistAppointments = $therapists->map(function ($therapist) {
            $total = $therapist->appointments()->where('status', '!=', 'cancelled')->count();
            return ['name' => $therapist->name, 'total_appointments' => $total];
        });

        $therapistNames = $therapistAppointments->pluck('name');
        $therapistAppointmentsCount = $therapistAppointments->pluck('total_appointments');

        // Appointment types distribution
        $appointmentTypes = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->get()
            ->groupBy('appointment_type')
            ->map->count();

        // Upcoming appointments for clinic (therapists)
        $appointments = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->with(['patient', 'therapist'])
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->get();

        return view('user.therapist.clinic.clinic', compact(
            'clinic',
            'totalTherapists',
            'totalEmployees',
            'totalPatients',
            'totalAppointments',
            'pendingAppointments',
            'therapists',
            'therapistNames',
            'therapistAppointmentsCount',
            'appointmentTypes',
            'appointments'
        ));
    }
    /** ---------------- PROFILE ---------------- */
    public function profile()
    {
        $data = $this->getProfileData();
        return view('user.therapist.clinic.profile', $data);
    }


    /** ---------------- SERVICES ---------------- */
    public function services()
    {
        $clinic = Auth::user();

        $existingServices = $this->getServices($clinic);

        $existingPrice = TherapistService::where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->value('price') ?? '';

        $schedules = Availability::where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->orderBy('day_of_week')
            ->get();

        $calendarSchedules = $this->getCalendarSchedules($schedules);

        return view('user.therapist.clinic.services', compact(
            'clinic',
            'schedules',
            'calendarSchedules',
            'existingServices',
            'existingPrice',
        ));
    }

    public function storeService(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'appointment_types' => 'array|nullable',
            'price' => 'nullable|string|max:255',
        ]);

        $appointmentTypes = $request->appointment_types ?? [];

        $this->saveServices($clinic, $appointmentTypes, $request->price);

        return back()->with('success', 'Appointment types updated!');
    }

    public function updateService(Request $request, $id)
    {
        $clinic = Auth::user();

        $service = TherapistService::where('id', $id)
            ->where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->firstOrFail();

        $request->validate([
            'appointment_types' => 'array|nullable',
            'price' => 'nullable|string|max:255',
        ]);

        $appointmentTypes = $request->appointment_types ?? [];

        $service->update([
            'appointment_type' => implode(',', $appointmentTypes),
            'price' => $request->price,
        ]);

        return back()->with('success', 'Service updated successfully!');
    }

    public function destroyService($id)
    {
        $clinic = Auth::user();

        $service = TherapistService::where('id', $id)
            ->where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->firstOrFail();

        $service->delete();

        return back()->with('success', 'Service deleted successfully!');
    }

    /** ---------------- AVAILABILITY / SCHEDULE ---------------- */
    public function availability()
    {
        $clinic = Auth::user();

        $services = $this->getServices($clinic);

        $schedules = Availability::where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->orderBy('day_of_week')
            ->get();

        $calendarSchedules = $this->getCalendarSchedules($schedules);

        return view('user.therapist.clinic.availability', compact('clinic', 'services', 'schedules', 'calendarSchedules'));
    }

    public function storeSchedule(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        Availability::create([
            'provider_id' => $clinic->id,
            'provider_type' => get_class($clinic),
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        return back()->with('success', 'Availability added successfully!');
    }

    public function toggleSchedule($id)
    {
        $clinic = Auth::user();
        $this->toggleAvailabilityById($id, $clinic);

        return back()->with('success', 'Schedule updated successfully!');
    }

    public function destroySchedule($id)
    {
        $clinic = Auth::user();
        $this->destroyAvailabilityById($id, $clinic);

        return back()->with('success', 'Schedule deleted successfully!');
    }

    /** ---------------- CLIENTS ---------------- */
    public function clients(Request $request)
    {
        $clinic = Auth::user();

        $therapistIds = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->pluck('id')
            ->toArray();

        $patients = $this->getPatientsFor($therapistIds, User::class);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $patients = $patients->filter(fn ($p) => str_contains(strtolower($p->name), $search));
        }

        if ($request->filled('gender')) {
            $patients = $patients->filter(fn ($p) => $p->gender === $request->gender);
        }

        return view('user.therapist.clients', compact('clinic', 'patients'));
    }

    /** ---------------- APPOINTMENTS ---------------- */
    public function appointments(Request $request)
    {
        $clinic = Auth::user();

        $therapistIds = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->pluck('id')
            ->toArray();

        $query = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->with(['patient', 'provider']);

        $query = $this->applyAppointmentFilters($query, $request);

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        $providers = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->get();

        return view('user.therapist.clinic.appointment', compact('appointments', 'providers'));
    }

    /** ---------------- EMPLOYEES ---------------- */
    public function employees(Request $request)
    {
        $clinic = Auth::user();

        $query = User::where('clinic_id', $clinic->id)
                 ->where('role', 'employee');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $employees = $query->get();

        return view('user.therapist.clinic.employees', compact('employees'));
    }

    public function storeEmployee(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        $profilePicture = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password123'), // default password
            'position' => $request->position,
            'role' => 'employee',
            'clinic_id' => $clinic->id,
            'profile_picture' => $profilePicture,
        ]);

        return back()->with('success', 'Employee added successfully!');
    }

    public function deleteEmployee($id)
    {
        $clinic = Auth::user();

        $employee = User::where('id', $id)
            ->where('clinic_id', $clinic->id)
            ->where('role', 'employee')
            ->firstOrFail();

        $employee->delete();

        return response()->json(['success' => true]);
    }

    public function getUnreadCounts()
    {
        $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        // For clinics: show pending appointments for all their therapists
        $clinicTherapistIds = \App\Models\User::where('clinic_id', auth()->id())
            ->where('role', 'therapist')
            ->pluck('id')
            ->toArray();
        
        $pendingAppointments = \App\Models\Appointment::whereIn('provider_id', $clinicTherapistIds)
            ->where('provider_type', \App\Models\User::class)
            ->where('status', 'pending')
            ->whereDate('appointment_date', '>=', now())
            ->count();
        
        return response()->json([
            'messages' => $unreadMessages,
            'appointments' => $pendingAppointments
        ]);
    }

}
