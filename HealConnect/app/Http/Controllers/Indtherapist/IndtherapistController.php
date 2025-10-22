<?php

namespace App\Http\Controllers\Indtherapist;

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


class IndtherapistController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $appointments = Appointment::where('therapist_id', $user->id)
            ->with('patient') // relationship in Appointment model
            ->orderBy('appointment_date', 'asc')
            ->take(3)
            ->get();

        $appointmentCount = Appointment::where('therapist_id', $user->id)
            ->distinct('patient_id')
            ->count('patient_id');

        return view('user.therapist.independent.independent', compact('user', 'appointments', 'appointmentCount'));
    }

    public function availability()
    {
        $user = Auth::user();

        // Fetch all availabilities of this therapist
        $availabilities = Availability::where('therapist_id', $user->id)
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->simplePaginate(3);

        $calendarAvailabilities = Availability::where('therapist_id', $user->id)->get();


        $services = DB::table('therapist_services')
            ->where('therapist_id', $user->id)
            ->value('appointment_type');

        $existingServices = $services ? explode(',', $services) : [];

        return view('user.therapist.independent.availability', compact('user','availabilities','calendarAvailabilities','existingServices'));
    }

    public function storeServices(Request $request)
    {
        $request->validate([
            'appointment_types' => 'required|array',
        ]);

        $user = auth()->user();

        DB::table('therapist_services')->updateOrInsert(
            ['therapist_id' => $user->id],
            ['appointment_type' => implode(',', $request->appointment_types)]
        );

        return back()->with('success', 'Your offered services have been updated successfully!');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
        $dayOfWeek = \Carbon\Carbon::parse($validated['date'])->format('l');

        Availability::create([
            'therapist_id' => Auth::id(),
            'date' => $validated['date'],
            'day_of_week' => $dayOfWeek,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);
        return back()->with('success', 'Availability added successfully.');
    }

    public function destroy($id)
    {
        $availability = Availability::where('therapist_id', Auth::id())->findOrFail($id);
        $availability->delete();

        return back()->with('success', 'Availability deleted successfully.');
    }
    public function toggleAvailability($id){
        $availability = Availability::where('therapist_id', Auth::id())->findOrFail($id);
        $availability->is_active = !$availability->is_active;
        $availability->save();

        return back()->with('success', 'Availability status updated successfully.');
    }

    public function settings()
        {
            $user = Auth::user();
            return view('user.therapist.independent.settings', compact('user'));
        }


    // View Appointments Page
    public function appointments()
        {
            $therapistId = Auth::id(); 

            $appointments = \App\Models\Appointment::where('therapist_id', $therapistId)
                ->with('patient')
                ->orderBy('appointment_date', 'asc')
                ->get();

            return view('user.therapist.Independent.appointment', compact('appointments'));
        }

    // Update Appointment Status
    public function updateAppointmentStatus(Request $request, $id)
        {
            $request->validate([
                'status' => 'required|in:approved,rejected,completed',
            ]);

            $appointment = Appointment::where('therapist_id', Auth::id())->findOrFail($id);
            $appointment->status = $request->status;
            $appointment->save();

            return back()->with('success', 'Appointment status updated successfully.');
        }


    // (Profile + Info + Password)
    public function profile()
    {
        $user = Auth::user();
        $availability = Availability::where('therapist_id', $user->id)
            ->orderBy('date', 'asc')
            ->get();

        $services= DB::table('therapist_services')
            ->where('therapist_id', $user->id)
            ->value('appointment_type');

        $servicesList = $services ? explode(',', $services) : [];

        return view('user.therapist.independent.profile', compact('user', 'availability', 'servicesList'));
    }


    public function updateProfile(Request $request) {
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

    public function updateInfo(Request $request) {
        $user = auth()->user();

        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'dob' => 'required|date',
            'gender' => 'required|string|in:male,female',
        ]);

        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->gender = $request->gender;

        $user->save();

        return back()->with('success', 'Info updated successfully!');
    }

    public function updatePassword(Request $request) {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
        }

    // View Patient Profile
    public function patientProfile($id)
    {
        $patient = User::where('role', 'patient')->findOrFail($id);

        return view('user.therapist.independent.patient_profile', compact('patient'));
    }
}
