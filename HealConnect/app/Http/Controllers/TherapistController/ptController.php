<?php

namespace App\Http\Controllers\TherapistController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\TherapistService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Mail\AppointmentStatusMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Events\AppointmentUpdateEvent;

class ptController extends Controller
{
    /**
     * Determine provider type based on logged-in user's role.
     * Returns an array suitable for where() e.g. ['provider_id' => X, 'provider_type' => Y::class]
     */
    protected function getProviderQuery(): array
    {
        $user = Auth::user();

        return [
            'provider_id' => $user->id,
            'provider_type' => User::class,
        ];
    }

    /** PROFILE SECTION */
    protected function getProfileData()
    {
        $user = Auth::user();

        $availability = Availability::where('provider_id', $user->id)
            ->where('provider_type', get_class($user))
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date', 'asc')
            ->get();

        $services = $this->getServices($user);

        $existingPrice = TherapistService::where('serviceable_id', $user->id)
            ->where('serviceable_type', get_class($user))
            ->value('price');

        return [
            'user' => $user,
            'availability' => $availability,
            'servicesList' => $services,
            'price' => $existingPrice,
        ];
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')
                    ->store('profile_pictures', 'public');

            $user->profile_picture = $path;
        }

        $user->save();

        return back()->with('success', 'Profile picture updated successfully.');
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        if ($request->has('description') && !$request->has('name')) {
            $request->validate([
                'description' => 'nullable|string|max:2000',
            ]);
            
            $user->description = $request->description;
            $user->save();
            
            return back()->with('success', 'Bio updated successfully.');
        }        

        $rules = [
            'name' => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:2000', 
            'specialization' => 'nullable|array',
            'specialization.*' => 'nullable|string|max:255',
            'clinic_license' => 'nullable|string|max:255', 
        ];

        $validated = $request->validate($rules);
        $specializations = $validated['specialization'] ?? [];
        $specializationString = $specializations ? implode(',', $specializations) : null;

        $user->update([
            'name' => $validated['name'],
            'phone'     => $validated['phone'] ?? $user->phone,
            'address'   => $validated['address'] ?? $user->address,
            'description'   => $validated['description'] ?? $user->description,
            'specialization' => $specializationString,
            'clinic_license' => $validated['clinic_license'] ?? $user->clinic_license,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'=> 'required',
            'new_password'=> 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Get appointment query for a provider.
     *
     * @param  int|array  $providerIdOrArray  (single id or array of ids)
     * @param  string     $providerType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function appointmentQueryFor($providerIdOrArray, $providerType = User::class)
    {
        $query = Appointment::query();

        if (is_array($providerIdOrArray)) {
            $query->whereIn('provider_id', $providerIdOrArray);
        } else {
            $query->where('provider_id', $providerIdOrArray);
        }

        $query->where('provider_type', $providerType);

        return $query;
    }

    /**
     * Shared dashboard data for a provider 
     */
    protected function getDashboardData()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $isClinic = $user->role === 'clinic';

        // Get provider IDs to query appointments
        $providerIds = $isClinic
            ? User::where('clinic_id', $user->id)->where('role', 'therapist')->pluck('id')->toArray()
            : [$user->id];

    
        $appointmentsQuery = Appointment::where('provider_id', $user->id)
                                    ->where('provider_type', User::class);

        // Upcoming appointments (next 3)
        $appointments = (clone $appointmentsQuery)->with('patient')
            ->get()
            ->filter(fn($a) => Carbon::parse($a->appointment_date . ' ' . $a->appointment_time)->greaterThan($now))
            ->sortBy('appointment_date')
            ->take(3);

        // Total unique clients
        $appointmentCount = (clone $appointmentsQuery)->distinct('patient_id')->count('patient_id');

        // Completed sessions
        $completedSessions = (clone $appointmentsQuery)->where('status', 'completed')->count();

        // Cancellations
        $cancellations = (clone $appointmentsQuery)->where('status', 'cancelled')->count();

        // Monthly appointments data
        $daysInMonth = $now->daysInMonth;
        $monthlyData = collect(range(1, $daysInMonth))->map(function ($day) use ($now, $appointmentsQuery) {
            $date = $now->copy()->startOfMonth()->addDays($day - 1)->toDateString();
            return (clone $appointmentsQuery)->whereDate('appointment_date', $date)->count();
        });

        // Appointments by type
        $appointmentTypes = (clone $appointmentsQuery)
            ->selectRaw('appointment_type, COUNT(*) as count')
            ->groupBy('appointment_type')
            ->pluck('count', 'appointment_type');

        return [
            'user' => $user,
            'appointments' => $appointments,
            'appointmentCount' => $appointmentCount,
            'completedSessions' => $completedSessions,
            'cancellations' => $cancellations,
            'monthlyData' => $monthlyData,
            'appointmentTypes' => $appointmentTypes,
        ];
    }

    /**
     * Shared settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('shared.settings', compact('user'));
    }

    /**
     * Fetch therapist/clinic services as array
     */
    public function getServices($therapist)
    {
        $service = TherapistService::where('serviceable_id', $therapist->id)
            ->where('serviceable_type', get_class($therapist))
            ->first();

        if (!$service) return [];

        return json_decode($service->appointment_type, true) ?? [];
    }

    /**
     * Save services (updateOrCreate)
     */
    protected function saveServices($provider, array $types = [], $price = null)
    {
        TherapistService::updateOrCreate(
            [
                'serviceable_id' => $provider->id,
                'serviceable_type' => User::class,
            ],
            [
                'appointment_type' => json_encode($types),
                'price' => $price,
            ]
        );
    }

    /**
     * Get calendar-ready schedules (common format)
     */
    protected function getCalendarSchedules($schedules)
    {
        return $schedules->map(function ($s) {
            return [
                'title' => 'Available',
                'day_of_week' => isset($s->day_of_week) ? (int) $s->day_of_week : null,
                'start_time' => $s->start_time ?? null,
                'end_time' => $s->end_time ?? null,
                'is_active' => $s->is_active ?? false,
                'extendedProps' => [
                    'is_active' => $s->is_active ?? false,
                ],
            ];
        })->toArray();
    }

    /**
     * Toggle availability (shared)
     */
    protected function toggleAvailabilityById($id, $provider)
    {
        $schedule = Availability::where('id', $id)
            ->where('provider_id', $provider->id)
            ->where('provider_type', get_class($provider))
            ->firstOrFail();

        $schedule->is_active = !$schedule->is_active;
        $schedule->save();

        return $schedule;
    }

    /**
     * Destroy availability (shared)
     */
    protected function destroyAvailabilityById($id, $provider)
    {
        $schedule = Availability::where('id', $id)
            ->where('provider_id', $provider->id)
            ->where('provider_type', get_class($provider))
            ->firstOrFail();

        $schedule->delete();
        return true;
    }

    /**
     * Shared patient list for a provider (provider_id/provider_type) or for array of provider ids.
     *
     * @param  int|array  $providerIdOrArray
     * @param  string     $providerType
     * @return \Illuminate\Support\Collection (unique patient models)
     */
    protected function getPatientsFor($providerIdOrArray, $providerType = User::class)
    {
        $query = Appointment::query();

        if (is_array($providerIdOrArray)) {
            $query->whereIn('provider_id', $providerIdOrArray);
        } else {
            $query->where('provider_id', $providerIdOrArray);
        }

        $query->where('provider_type', $providerType)
            ->with('patient');

        $appointments = $query->get();

        return $appointments->pluck('patient')->unique('id')->values();
    }

    /**
     * Shared appointment filter helper that mutates a query builder.
     */
    protected function applyAppointmentFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })->orWhere('appointment_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('appointment_type', $request->input('type'));
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->input('provider_id'));
        }

        return $query;
    }

    /**
     * View patient profile 
     */
    public function patientProfile($patientId)
    {
        $user = Auth::user();
        $patient = User::findOrFail($patientId);

        // Restrict access to only patients under this provider
        $isLinked = Appointment::where('provider_id', $user->id)
            ->where('provider_type', User::class)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$isLinked) {
            abort(403, 'Unauthorized access to this patient.');
        }

        // Get appointment history for this patient with this provider
        $appointments = Appointment::where('provider_id', $user->id)
            ->where('provider_type', User::class)
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('user.therapist.patient_profile', compact('patient', 'appointments'));
    }

    /**
     * Patient medical records view
     */
    public function patient_records($patientId)
    {
        $user = Auth::user();
        $patient = User::findOrFail($patientId);

        // Restrict access to only patients under this provider
        $isLinked = Appointment::where('provider_id', $user->id)
            ->where('provider_type', User::class)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$isLinked) {
            abort(403, 'Unauthorized access to this patient.');
        }

        return view('User.Therapist.patients_records', [
            'patient' => $patient,
            'ehr' => $patient->ehr ?? null,
            'therapies' => $patient->therapies ?? null,
            'exercises' => $patient->exercises ?? null,
        ]);
    }

    /**
     * Update Electronic Health Record (EHR)
     */
    public function updateEHR(Request $request, $patientId)
    {
        $request->validate([
            'ehr' => 'required|string',
        ]);

        $patient = User::findOrFail($patientId);
        $patient->ehr = $request->ehr;
        $patient->save();

        return redirect()->back()->with('success', 'EHR updated successfully!');
    }

    /**
     * Update Treatment Plan
     */
    public function updateTreatment(Request $request, $patientId)
    {
        $request->validate([
            'treatment_plan' => 'required|string',
        ]);

        $patient = User::findOrFail($patientId);
        $patient->therapies = $request->treatment_plan;
        $patient->save();

        return redirect()->back()->with('success', 'Treatment plan updated successfully!');
    }

    /**
     * Update Progress Notes
     */
    public function updateProgress(Request $request, $patientId)
    {
        $request->validate([
            'progress_note' => 'required|string',
        ]);

        $patient = User::findOrFail($patientId);
        $patient->exercises = $request->progress_note;
        $patient->save();

        return redirect()->back()->with('success', 'Progress note updated successfully!');
    }

    /**
     * Update appointment status (approved, rejected, completed)
     * Sends email notification to patient and updates availability where applicable.
     */
    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed,pending',
        ]);

        $appointment = Appointment::findOrFail($id);
        $patientId = $appointment->patient_id;
        
        $appointment->status = $request->input('status');
        $appointment->save();

        // Update related availability (if any)
        try {
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
        } catch (\Exception $e) {
            Log::error("Availability update failed: " . $e->getMessage());
        }

        // Send email notification to patient 
        try {
            Mail::to(optional($appointment->patient)->email)
                ->send(new AppointmentStatusMail($appointment));
        } catch (\Exception $e) {
            Log::error("Failed to send appointment email: " . $e->getMessage());
        }

        // Broadcast notification to patient about appointment status change
        $this->broadcastAppointmentNotification($patientId);

        return back()->with('success', 'Appointment status updated and patient notified!');
    }

    /**
     * Broadcast appointment notification to a user
     */
    private function broadcastAppointmentNotification($userId)
    {
        $appointmentCount = Appointment::where('patient_id', $userId)
            ->where('status', 'pending')
            ->whereDate('appointment_date', '>=', now())
            ->count();
        broadcast(new AppointmentUpdateEvent($userId, $appointmentCount));
    }

    public function getUnreadCounts()
    {
        $userId = auth()->id();

        $unreadMessages = \App\Models\Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        $pendingAppointments = \App\Models\Appointment::where('provider_id', $userId)
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