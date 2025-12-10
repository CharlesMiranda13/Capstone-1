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
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;

        // Count therapists under the clinic
        $totalTherapists = User::where('clinic_id', $clinic->id)
            ->whereIn('role', ['therapist', 'independent_therapist'])
            ->count();

        // Counts for employees
        $totalEmployees = User::where('clinic_id', $clinic->id)
            ->where('role', 'employee')->count();

        // Total unique patients for clinic
        $totalPatients = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class)
            ->with('patient')
            ->get()
            ->pluck('patient')
            ->unique('id')
            ->count();

        $totalAppointments = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class)
            ->count();

        $pendingAppointments = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class)
            ->where('status', 'pending')
            ->count();

        // Appointment types distribution
        $appointmentTypes = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class)
            ->get()
            ->groupBy('appointment_type')
            ->map->count();

        // Upcoming appointments
        $appointments = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class)
            ->with(['patient', 'therapist'])
            ->where(function($query) {
                $query->whereDate('appointment_date', '>', today())
                    ->orWhere(function($q) {
                        $q->whereDate('appointment_date', '=', today())
                        ->whereTime('appointment_time', '>=', now()->format('H:i:s'));
                    });
            })
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
        $appointmentsQuery = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class);

        $monthlyData = collect(range(1, $daysInMonth))->map(function ($day) use ($now, $appointmentsQuery) {
            $date = $now->copy()->startOfMonth()->addDays($day - 1)->toDateString();
            return (clone $appointmentsQuery)->whereDate('appointment_date', $date)->count();
        });

        return view('user.therapist.clinic.clinic', compact(
            'clinic',
            'totalEmployees',
            'totalPatients',
            'totalAppointments',
            'pendingAppointments',
            'appointmentTypes',
            'appointments',
            'totalTherapists',
            'monthlyData'
        ));
    }


    /** ---------------- PROFILE ---------------- */
    public function profile()
    {
        $clinic = Auth::user();

        // Get clinic-specific profile data (overrides parent method)
        $data = $this->getClinicProfileData();

        // Fetch employees
        $employees = User::where('clinic_id', $clinic->id)
                        ->where('role', 'employee')
                        ->get();

        // Merge employees into the data array
        $data['employees'] = $employees;

        return view('user.therapist.clinic.profile', $data);
    }

    /**
     * Get profile data for clinic (weekly recurring schedules)
     * Overrides the parent method which uses date-based availability
     */
    protected function getClinicProfileData()
    {
        $clinic = Auth::user();

        // Fetch weekly recurring schedules (day_of_week based)
        $schedules = Availability::where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->orderBy('day_of_week')
            ->get();

        $services = $this->getServices($clinic);

        $existingPrice = TherapistService::where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->value('price');

        return [
            'user' => $clinic,
            'schedules' => $schedules,
            'servicesList' => $services,
            'price' => $existingPrice,
        ];
    }

    /** ---------------- SERVICES ---------------- */
    public function services()
    {
        $clinic = Auth::user();

        $existingServices = $this->getServices($clinic);

        $existingPrice = TherapistService::where('serviceable_id', $clinic->id)
            ->where('serviceable_type', get_class($clinic))
            ->value('price') ?? '';

        // For table display
        $schedules = Availability::where('provider_id', $clinic->id)
            ->where('provider_type', get_class($clinic))
            ->orderBy('day_of_week')
            ->get();

        // For calendar display
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

        // Get patients who have appointments with THIS clinic
        $patients = $this->getPatientsFor($clinic->id, User::class);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $patients = $patients->filter(fn ($p) => str_contains(strtolower($p->name), $search));
        }

        if ($request->filled('gender')) {
            $patients = $patients->filter(fn ($p) => $p->gender === $request->gender);
        }

        return view('user.therapist.client', compact('clinic', 'patients'));
    }

    /** ---------------- APPOINTMENTS ---------------- */
    public function appointments(Request $request)
    {
        $clinic = Auth::user();

        $query = Appointment::where('provider_id', $clinic->id)
            ->where('provider_type', User::class)
            ->with(['patient', 'provider']);

        $query = $this->applyAppointmentFilters($query, $request);
        $appointments = $query->orderBy('appointment_date', 'desc')
        ->orderBy('appointment_time', 'desc')
        ->get();

    // Get unique patients (latest appointment for each patient)
    $appointments = $appointments->unique('patient_id')->values();

    // Add record count for each patient
    foreach ($appointments as $appointment) {
        $appointment->record_count = Appointment::where('provider_id', $clinic->id)
            ->where('patient_id', $appointment->patient_id)
            ->where('status', 'completed')
            ->count();
    }

    return view('user.therapist.clinic.appointment', compact('appointments'));
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

        $profilePicture = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password123'),
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

    public function unreadCounts()
    {
        return $this->getUnreadCounts();
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        return parent::updateAppointmentStatus($request, $id);
    }
}
