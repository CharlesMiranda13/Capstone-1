<?php

namespace App\Http\Controllers\Clinictherapist;

use App\Http\Controllers\TherapistController\ptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Availability;
use Carbon\Carbon;

class ClinicController extends ptController
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $data = $this->getDashboardData();
        return view('user.therapist.clinic.clinic', $data);
    }

    /** ---------------- CLIENTS ---------------- */
    public function clients(Request $request)
    {
        $clinic = Auth::user();

        // Get therapists under this clinic
        $therapistIds = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->pluck('id');

        // Get appointments only of these therapists AND correct provider_type
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

        // Get IDs of therapists under this clinic
        $therapistIds = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->pluck('id');

        // Query only appointments of these therapists
        $query = Appointment::whereIn('provider_id', $therapistIds)
            ->where('provider_type', User::class) // ensure only clinic therapists
            ->with(['patient', 'provider']);

        // Search filter by patient name or appointment type
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', fn($subQuery) => $subQuery->where('name', 'like', "%$search%"))
                  ->orWhere('appointment_type', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by appointment type
        if ($request->filled('type')) {
            $query->where('appointment_type', $request->type);
        }

        // Filter by provider (therapist)
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        // Get appointments ordered by date descending
        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        // Get list of providers for the filter dropdown
        $providers = User::where('role', 'therapist')
            ->where('clinic_id', $clinic->id)
            ->get();

        return view('user.therapist.clinic.appointment', compact('appointments', 'providers'));
    }

    /** ---------------- SETTINGS ---------------- */
    public function settings()
    {
        $clinic = Auth::user();
        return view('shared.settings', compact('clinic'));
    }

    public function updateProfile(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $clinic->profile_picture = $path;
        }

        $clinic->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateInfo(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $clinic->id,
        ]);

        $clinic->update($request->only('address', 'phone', 'email'));

        return back()->with('success', 'Info updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $clinic->password = bcrypt($request->password);
        $clinic->save();

        return back()->with('success', 'Password updated successfully!');
    }
}
