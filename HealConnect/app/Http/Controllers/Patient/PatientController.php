<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use App\Models\MedicalRecord;

class PatientController extends Controller
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Filter out cancelled appointments
        $appointments = Appointment::where('patient_id', $user->id)
            ->whereRaw("STR_TO_DATE(CONCAT(appointment_date, ' ', appointment_time), '%Y-%m-%d %H:%i:%s') >= NOW()")
            ->whereIn('status', ['pending','approved','confirmed','completed'])
            ->with('provider')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(3)
            ->get();
            
        $therapists = User::verifiedTherapists()->get();

        $recentRecords = MedicalRecord::where('patient_id', $user->id)
        ->with('therapist:id,name')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        $latestProgressNote = null;
        if (!empty($user->exercises)) {
            $progressEntries = array_filter(explode("\n\n", $user->exercises));
            if (count($progressEntries) > 0) {
                $latestEntry = end($progressEntries);
                
                $parts = explode(": ", $latestEntry, 2);
                if (count($parts) == 2) {
                    $latestProgressNote = [
                        'date' => $parts[0],
                        'note' => $parts[1]
                    ];
                }
            }
        }
        
        $latestProgressRecord = MedicalRecord::where('patient_id', $user->id)
            ->where('record_type', 'Progress Note')
            ->with('therapist:id,name')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('user.patients.patient', compact('user', 'appointments', 'therapists', 'recentRecords', 'latestProgressNote', 'latestProgressRecord'));
    }

    /** ---------------- LIST OF THERAPISTS ---------------- */
    public function listOfTherapist(Request $request)
    {
        $therapists = $this->filterTherapists($request);

        $patientHasApprovedReferral = Referral::where('patient_id', Auth::id())
            ->where('status', 'approved')
            ->exists();

        return view('user.patients.listoftherapist', compact('therapists', 'patientHasApprovedReferral'));
    }

    public function publicTherapists(Request $request)
    {
        $therapists = $this->filterTherapists($request);
        return view('ptlist', compact('therapists'));
    }

    /** ---------------- THERAPIST PROFILE ---------------- */
    public function showProfile($id)
    {
        $therapist = $this->loadTherapist($id);
        return view('user.patients.therapist_profile', $therapist);
    }

    public function publicTherapistProfile($id)
    {
        $therapist = $this->loadTherapist($id);
        return view('view_profile', $therapist);
    }

    /** ---------------- SETTINGS ---------------- */
    public function settings()
    {
        $user = Auth::user();
        return view('shared.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
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
        $user = Auth::user();
        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($request->only('address','phone','email','dob','gender'));
        return back()->with('success', 'Info updated successfully!');
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    /** ---------------- PRIVATE HELPERS ---------------- */

    private function filterTherapists(Request $request)
    {
        $query = User::verifiedTherapists()
            ->where('subscription_status', 'active')
            ->with(['services', 'availability']); 

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('services', fn($s) =>
                    $s->where('appointment_type', 'like', "%{$search}%")
                );
            });
        }

        if ($request->filled('category')) {
            $query->where('role', $request->category === 'independent' ? 'therapist' : 'clinic');
        }

        if ($request->filled('service')) {
            $query->whereHas('services', fn($q) =>
                $q->where('appointment_type', 'like', "%{$request->service}%")
            );
        }
        
        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        if ($request->filled('province')) {
            $query->where('province', 'like', "%{$request->province}%");
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        return $query->paginate(10)->withQueryString();
    }

    private function loadTherapist($id)
    {
        $therapist = User::verifiedTherapists()->with('services')->findOrFail($id);

        $servicesList = $therapist->services
            ->pluck('appointment_type')
            ->map(fn($s) => explode(',', $s))
            ->flatten()
            ->all();
    
        // Full services data with price/fee
        $servicesData = $therapist->services
            ->map(function($service) {
                return [
                    'appointment_types' => explode(',', $service->appointment_type),
                    'price' => $service->price ?? 'Please inquire',
                ];
            });

        $therapistAvailability = $therapist->upcomingAvailability();
        $price = $therapist->services->first()?->price ?? null;

        // Fetch employees if therapist is a clinic
        $employees = collect();
        
        // Check if therapist is a clinic (has employees)
        if (in_array($therapist->role, ['clinic', 'therapist']) || 
            str_contains(strtolower($therapist->role_display ?? ''), 'clinic')) {
            
            $employees = User::where('clinic_id', $therapist->id)
                             ->where('role', 'employee')
                             ->select('id', 'name', 'gender', 'position', 'profile_picture')
                             ->orderBy('name')
                             ->get();
        }

        return compact('therapist', 'servicesList', 'servicesData', 'therapistAvailability', 'price', 'employees');
    }

    public function getUnreadCounts()
    {
        $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        // Also exclude cancelled appointments from notification count
        $upcomingAppointments = \App\Models\Appointment::where('patient_id', auth()->id())
            ->whereIn('status', ['pending', 'confirmed']) 
            ->whereDate('appointment_date', '>=', now())
            ->count();
        
        return response()->json([
            'messages' => $unreadMessages,
            'appointments' => $upcomingAppointments
        ]);
    }

    public function records()
    {
        $user = Auth::user();
        
        if ($user->role !== 'patient') {
            abort(403, 'Unauthorized access');
        }

        $records = MedicalRecord::where('patient_id', $user->id)
            ->with('therapist:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.patients.records', compact('records'));
    }

    public function myRecords(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'patient') {
            abort(403, 'Unauthorized access');
        }

        $recordId = $request->query('record_id');
        $timestamp = $request->query('timestamp');
        
        if (!$recordId || !$timestamp) {
            abort(404, 'Record not found');
        }

        // Find the specific medical record
        $record = MedicalRecord::where('id', $recordId)
            ->where('patient_id', $user->id)
            ->firstOrFail();
        
        // Verify the timestamp matches (security check)
        if ($record->created_at->timestamp != $timestamp) {
            abort(403, 'Invalid record access');
        }

        // Get the patient
        $patient = $user;
        
        // Get all records up to this point, ordered from OLDEST to NEWEST
        $recordsUpToThisPoint = MedicalRecord::where('patient_id', $user->id)
            ->where('created_at', '<=', $record->created_at)
            ->orderBy('created_at', 'asc') // Oldest first for building
            ->get();
        
        // For EHR: Only get the LATEST/MOST RECENT one
        $ehr = null;
        
        // For therapies and exercises: Build cumulative content
        $therapies = '';
        $exercises = '';
        
        foreach ($recordsUpToThisPoint as $historicalRecord) {
            switch ($historicalRecord->changed_field) {
                case 'ehr':
                    // Replace with the latest EHR
                    $ehr = $historicalRecord->changed_data;
                    break;
                case 'therapies':
                    // Prepend new data to the top
                    $newData = $historicalRecord->changed_data;
                    $therapies = $newData . (!empty($therapies) ? "\n\n" . $therapies : '');
                    break;
                case 'exercises':
                    // Prepend new data to the top
                    $newData = $historicalRecord->changed_data;
                    $exercises = $newData . (!empty($exercises) ? "\n\n" . $exercises : '');
                    break;
            }
        }
        
        // Convert empty strings to null
        $therapies = !empty($therapies) ? $therapies : null;
        $exercises = !empty($exercises) ? $exercises : null;
        
        return view('user.patients.my_records', [
            'patient' => $patient,
            'record' => $record,
            'ehr' => $ehr,
            'therapies' => $therapies,
            'exercises' => $exercises,
            'recordDateTime' => $record->created_at,
            'changedField' => $record->changed_field,
        ]);
    }

}