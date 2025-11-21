<?php

namespace App\Http\Controllers\Clinictherapist;

use App\Http\Controllers\TherapistController\ptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Availability;
use App\Models\TherapistService;

class ClinicController extends ptController
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $clinic = Auth::user();

        $totalTherapists = User::where('clinic_id', $clinic->id)->where('role', 'therapist')->count();
        $totalEmployees = User::where('clinic_id', $clinic->id)->where('role', 'employee')->count();

        $totalPatients = Appointment::whereHas('provider', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'therapist');
        })->with('patient')->get()->pluck('patient')->unique('id')->count();

        $totalAppointments = Appointment::whereHas('provider', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'therapist');
        })->count();

        $pendingAppointments = Appointment::whereHas('provider', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'therapist');
        })->where('status', 'pending')->count();

        $therapists = User::where('clinic_id', $clinic->id)->where('role','therapist')->get();
        $employees = User::where('clinic_id', $clinic->id)->where('role','employee')->get();

        $therapistAppointments = $therapists->map(function($therapist) {
            $total = $therapist->appointments()->where('status','!=','cancelled')->count();
            return ['name' => $therapist->name, 'total_appointments' => $total];
        });

        $therapistNames = $therapistAppointments->pluck('name');
        $therapistAppointmentsCount = $therapistAppointments->pluck('total_appointments');

        $appointmentTypes = Appointment::whereHas('provider', function($q) use ($clinic){
            $q->where('clinic_id', $clinic->id)->where('role','therapist');
        })->get()->groupBy('appointment_type')->map->count();

        $appointments = Appointment::whereHas('provider', function($q) use ($clinic){
            $q->where('clinic_id', $clinic->id)->where('role','therapist');
        })->with(['patient','therapist'])->where('appointment_date', '>=', now())->orderBy('appointment_date')->get();

        return view('user.therapist.clinic.clinic', compact(
            'clinic',
            'totalTherapists',
            'totalEmployees',
            'totalPatients',
            'totalAppointments',
            'pendingAppointments',
            'therapists',
            'employees',
            'therapistNames',
            'therapistAppointmentsCount',
            'appointmentTypes',
            'appointments'
        ));
    }

    /** ---------------- SERVICES ---------------- */
    public function services()
    {
        $clinic = Auth::user();

        $service = TherapistService::where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->first();

        $existingServices = [];
        if ($service && $service->appointment_type) {
            $existingServices = explode(',', $service->appointment_type); // convert string to array
        }

        $existingPrice = $service->price ?? '';

        $schedules = Availability::where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->orderBy('day_of_week')
            ->get();

        $calendarSchedules = $schedules->map(function($s) {
            return [
                'title' => 'Available',
                'daysOfWeek' => [(int)$s->day_of_week],
                'startTime' => $s->start_time,
                'endTime' => $s->end_time,
                'extendedProps' => ['is_active' => $s->is_active],
            ];
        });

        return view('user.therapist.clinic.services', compact(
            'clinic',
            'schedules',
            'calendarSchedules',
            'existingServices',
            'existingPrice'
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

        TherapistService::updateOrCreate(
            [
                'serviceable_id' => $clinic->id,
                'serviceable_type' => get_class($clinic),
            ],
            [
                'appointment_type' => implode(',', $appointmentTypes),
                'price' => $request->price,
            ]
        );

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
            'appointment_type' => json_encode($appointmentTypes),
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

        $services = TherapistService::where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->get();

        $schedules = Availability::where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->orderBy('day_of_week')
            ->get();

        $calendarSchedules = $schedules->map(function($s) {
            return [
                'title' => 'Available',
                'day_of_week' => (int) $s->day_of_week,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'is_active' => $s->is_active,
                'color' => $s->is_active ? '#4c8bf5' : '#e0e0e0',
                'extendedProps' => [
                    'is_active' => $s->is_active,
                ],
            ];
        });

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

        $schedule = Availability::where('id', $id)
            ->where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->firstOrFail();

        $schedule->is_active = !$schedule->is_active;
        $schedule->save();

        return back()->with('success', 'Schedule updated successfully!');
    }

    public function destroySchedule($id)
    {
        $clinic = Auth::user();

        $schedule = Availability::where('id', $id)
            ->where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->firstOrFail();

        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully!');
    }

    /** ---------------- CLIENTS ---------------- */
    public function clients(Request $request)
    {
        $clinic = Auth::user();

        $therapistIds = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->pluck('id');

        $appointments = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->with('patient')
            ->get();

        $patients = $appointments->pluck('patient')->unique('id');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $patients = $patients->filter(fn($p) => str_contains(strtolower($p->name), $search));
        }

        if ($request->filled('gender')) {
            $patients = $patients->filter(fn($p) => $p->gender === $request->gender);
        }

        return view('user.therapist.clients', compact('clinic', 'patients'));
    }

    /** ---------------- APPOINTMENTS ---------------- */
    public function appointments(Request $request)
    {
        $clinic = Auth::user();

        $therapistIds = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->pluck('id');

        $query = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class)
            ->with(['patient', 'provider']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', fn($subQuery) => $subQuery->where('name', 'like', "%$search%"))
                  ->orWhere('appointment_type', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('appointment_type', $request->type);
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        $providers = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->get();

        return view('user.therapist.clinic.appointment', compact('appointments', 'providers'));
    }
}
