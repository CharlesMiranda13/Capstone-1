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
use App\Mail\AppointmentStatusMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ptController extends Controller
{
    /**
     * Determine provider type based on logged-in user's role.
     * Returns an array suitable for where() e.g. ['provider_id' => X, 'provider_type' => Y::class]
     */
    protected function getProviderQuery(): array
    {
        $user = Auth::user();

        $providerType = $user->role === 'clinic'
            ? \App\Models\Clinic::class
            : \App\Models\User::class;

        return [
            'provider_id' => $user->id,
            'provider_type' => $providerType,
        ];
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
     * Shared dashboard data for a provider (independent therapists).
     * Clinic controllers may call this for provider-level metrics or reuse parts as needed.
     */
    protected function getDashboardData()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Build Appointment query for this provider type and id
        $providerType = $user->role === 'clinic' ? \App\Models\Clinic::class : \App\Models\User::class;
        $appointmentsQuery = Appointment::where('provider_id', $user->id)
                                    ->where('provider_type', $providerType);

        // Upcoming appointments (next 3)
        $appointments = $appointmentsQuery->with('patient')
            ->get()
            ->filter(function ($a) use ($now) {
                $dt = Carbon::parse($a->appointment_date . ' ' . $a->appointment_time);
                return $dt->greaterThan($now);
            })
            ->sortBy('appointment_date')
            ->take(3);

        // Total unique clients
        $appointmentCount = $appointmentsQuery->distinct('patient_id')->count('patient_id');

        // Completed sessions
        $completedSessions = $appointmentsQuery->where('status', 'completed')->count();

        // Cancellations
        $cancellations = $appointmentsQuery->where('status', 'cancelled')->count();

        // Monthly appointments data (for line chart) - counts per day of current month
        $nowCopy = $now->copy(); // avoid mutating original $now
        $daysInMonth = $nowCopy->daysInMonth;
        $monthlyData = collect(range(1, $daysInMonth))->map(function ($d) use ($nowCopy, $appointmentsQuery) {
            $date = $nowCopy->copy()->startOfMonth()->addDays($d - 1)->toDateString();
            return $appointmentsQuery->whereDate('appointment_date', $date)->count();
        });

        // Appointments by type
        $appointmentTypes = $appointmentsQuery->selectRaw('appointment_type, COUNT(*) as count')
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
    protected function getServices($provider)
    {
        $raw = TherapistService::where('serviceable_id', $provider->id)
            ->where('serviceable_type', get_class($provider))
            ->value('appointment_type');

        return $raw ? array_filter(array_map('trim', explode(',', $raw))) : [];
    }

    /**
     * Save services (updateOrCreate)
     */
    protected function saveServices($provider, array $types = [], $price = null)
    {
        TherapistService::updateOrCreate(
            [
                'serviceable_id' => $provider->id,
                'serviceable_type' => get_class($provider),
            ],
            [
                'appointment_type' => implode(',', $types),
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
     * Patient medical records view
     */
    public function patient_records($patientId)
    {
        $patient = User::findOrFail($patientId);

        // restrict access to only patients under this provider
        $provider = $this->getProviderQuery();
        $isLinked = Appointment::where($provider)
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

        return back()->with('success', 'Appointment status updated and patient notified!');
    }
}
