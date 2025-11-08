<?php

namespace App\Http\Controllers\Indtherapist;

use App\Http\Controllers\TherapistController\ptController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\Record;
use App\Models\Referral;
use App\Models\Notification;
use App\Models\User;
use App\Models\Availability;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IndtherapistController extends ptController
{
    /**  DASHBOARD */
    public function dashboard()
    {
        $data = $this->getDashboardData();
        return view('user.therapist.independent.independent', $data);
    }


    /**  AVAILABILITY */
    public function availability()
    {
        $user = Auth::user();

        $availabilities = Availability::with('appointments')
            ->where('provider_id', $user->id)
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->simplePaginate(3);

        $calendarAvailabilities = Availability::where('provider_id', $user->id)->get();

        $services = DB::table('therapist_services')
            ->where('serviceable_id', $user->id)
            ->where('serviceable_type', get_class($user))
            ->value('appointment_type');

        $existingServices = $services ? explode(',', $services) : [];

        return view('user.therapist.independent.availability', compact(
            'user', 'availabilities', 'calendarAvailabilities', 'existingServices'
        ));
    }

    public function storeServices(Request $request)
    {
        $request->validate([
            'appointment_types' => 'required|array',
        ]);

        $user = auth()->user();

        DB::table('therapist_services')->updateOrInsert(
            [
                'serviceable_id' => $user->id,
                'serviceable_type' => get_class($user),
            ],
            ['appointment_type' => implode(',', $request->appointment_types)]
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
        $availability = Availability::where('provider_id', Auth::id())->findOrFail($id);
        $availability->delete();

        return back()->with('success', 'Availability deleted successfully.');
    }

    public function toggleAvailability($id)
    {
        $availability = Availability::where('provider_id', Auth::id())->findOrFail($id);
        $availability->is_active = !$availability->is_active;
        $availability->save();

        return back()->with('success', 'Availability status updated successfully.');
    }

    /** ---------------- SETTINGS ---------------- */
    public function settings()
    {
        $user = Auth::user();
        return view('shared.settings', compact('user'));
    }

    /** ---------------- APPOINTMENTS ---------------- */

    public function appointments(Request $request)
    {
        $user = Auth::user();

        // Base query for therapist’s appointments
        $query = Appointment::where('provider_id', $user->id)
            ->with('patient');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('appointment_type', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by appointment type
        if ($request->filled('type')) {
            $query->where('appointment_type', $request->input('type'));
        }

        // Get the latest appointment per patient
        $appointments = $query
            ->select('appointments.*')
            ->join(DB::raw('(SELECT MAX(id) as latest_id FROM appointments GROUP BY patient_id) as latest'), 'appointments.id', '=', 'latest.latest_id')
            ->orderBy('appointment_date', 'desc')
            ->get();

        // Mark returning patients (those with completed history)
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
        $request->validate([
            'status' => 'required|in:approved,rejected,completed',]);

        $appointment = Appointment::forProvider(Auth::user())->findOrFail($id);
        $appointment->status = $request->status;
        $appointment->save();

        // Find related availability
        $availability = Availability::where('provider_id', $appointment->provider_id)
            ->whereDate('date', $appointment->appointment_date)
            ->first();

        if ($availability) {
            if ($request->status === 'completed') {
                $availability->status = 'completed';
                $availability->is_active = false;
            } elseif (in_array($request->status, ['approved', 'pending'])) {
                $availability->status = 'active';
                $availability->is_active = true;
            } elseif ($request->status === 'rejected') {
                $availability->status = 'cancelled';
                $availability->is_active = false;
            }

            $availability->save();
        }

        return back()->with('success', 'Appointment status updated successfully!');
    }
    /** ---------------- Clients ---------------- */
    public function clients(Request $request) 
    {
        $user = Auth::user();
        $patients = User::where('role', 'patient')->get(); 
        return view('User.Therapist.client', compact('user', 'patients'));
    }


    /** ---------------- PROFILE ---------------- */
    public function profile()
    {
        $user = Auth::user();
        $availability = Availability::where('provider_id', $user->id)
            ->orderBy('date', 'asc')
            ->get();

        $services = DB::table('therapist_services')
            ->where('serviceable_id', $user->id)
            ->where('serviceable_type', get_class($user))
            ->value('appointment_type');

        $servicesList = $services ? explode(',', $services) : [];

        return view('user.therapist.independent.profile', compact('user', 'availability', 'servicesList'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'dob' => 'required|date',
            'gender' => 'required|string|in:male,female',
        ]);

        $user->update($request->only('address', 'phone', 'email', 'dob', 'gender'));

        return back()->with('success', 'Info updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    /** ---------------- PATIENT PROFILE ---------------- */
    public function patientProfile($id)
    {
        $patient = User::where('role', 'patient')->findOrFail($id);
        $appointments = $patient->appointments ?? [];
        return view('user.therapist.independent.patient_profile', compact('patient', 'appointments'));
    }
}
