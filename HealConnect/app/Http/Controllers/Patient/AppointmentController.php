<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Events\AppointmentUpdateEvent;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /** ---------------- LIST APPOINTMENTS ---------------- */
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id())
            ->with('provider')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('user.patients.appointments', compact('appointments'));
    }

    /** ---------------- BOOKING FORM ---------------- */
    public function create($therapistId)
    {
        $therapist = User::whereIn('role', ['therapist', 'clinic'])
            ->where('id', $therapistId)
            ->firstOrFail();

        // Get Services (using polymorphic relationship)
        $servicesList = [];
        $servicesData = $therapist->services()->first();
        
        if ($servicesData && $servicesData->appointment_type) {
            $servicesList = (array) $servicesData->appointment_type;
        }

        // Get Availabilities based on role
        if ($therapist->role === 'therapist') {
            // Independent therapist: specific dates from availability (polymorphic)
            $therapistAvailability = $therapist->availability()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereDate('date', '>', now()->toDateString())
                      ->orWhere(function ($q2) {
                          $q2->whereDate('date', now()->toDateString())
                             ->whereTime('end_time', '>', now()->format('H:i:s'));
                      });
                })
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get(['id', 'date', 'start_time', 'end_time', 'day_of_week'])
                ->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'date' => $slot->date,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'day_of_week' => $slot->day_of_week ?? Carbon::parse($slot->date)->format('l'),
                    ];
                });
        } elseif ($therapist->role === 'clinic') {
            // Clinic: generate specific dates from weekly schedule
            $therapistAvailability = $this->generateClinicAvailabilities($therapist);
        } else {
            $therapistAvailability = collect([]);
        }

        // Get Booked times
        $bookedTimes = Appointment::where('provider_id', $therapistId)
            ->where('provider_type', get_class($therapist))
            ->whereDate('appointment_date', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->groupBy('appointment_date')
            ->map(fn($group) => $group->pluck('appointment_time')->toArray());

        return view('user.patients.appointment_booking', compact(
            'therapist',
            'servicesList',
            'therapistAvailability',
            'bookedTimes'
        ));
    }

    /**
     * Generate specific date availabilities from clinic's weekly schedule
     * Uses the polymorphic availability relationship
     */
    private function generateClinicAvailabilities($clinic)
    {
        $schedules = $clinic->availability()
            ->where('is_active', true)
            ->whereNotNull('day_of_week') // Clinic schedules have day_of_week
            ->get();
        
        if ($schedules->isEmpty()) {
            return collect([]);
        }
        
        $availabilities = collect([]);
        $startDate = now();
        $endDate = now()->addDays(30); // Generate for next 30 days
        
        // Loop through each day in the range
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
            
            // Find schedules for this day of week
            $daySchedules = $schedules->where('day_of_week', $dayOfWeek);
            
            foreach ($daySchedules as $schedule) {
                // Skip if this time slot has already passed today
                if ($date->isToday() && now()->format('H:i:s') >= $schedule->end_time) {
                    continue;
                }
                
                $availabilities->push([
                    'id' => $schedule->id,
                    'date' => $date->toDateString(),
                    'day_of_week' => $date->format('l'), 
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ]);
            }
        }
        
        return $availabilities->sortBy('date')->values();
    }

    /** ---------------- STORE APPOINTMENT ---------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'therapist_id' => 'required|exists:users,id',
            'appointment_type' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
            'referral' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $therapist = User::findOrFail($validated['therapist_id']);

        if (!$therapist->canAccessSystem()) {
            return back()->withInput()->with('error', 'This therapist/clinic is currently not accepting new appointments due to subscription status.');
        }

        // Explicitly enforce the Trial Limit (2 patients max) for direct booking bypassers
        if ($therapist->subscription_status === 'inactive') {
            $patientId = auth()->id();
            
            // Check if this patient is ALREADY a customer of this provider
            $isExistingPatient = Appointment::where('provider_id', $therapist->id)
                ->where('provider_type', get_class($therapist))
                ->where('patient_id', $patientId)
                ->exists();

            // If they are NOT an existing patient, and the therapist is at max capacity (2+), block the booking
            if (!$isExistingPatient && $therapist->customer_count >= 2) {
                return back()->withInput()->with('error', 'This therapist has reached their patient capacity on the free trial and cannot accept new patients at this time.');
            }
        }

        // Validate availability based on role 
        if ($therapist->role === 'therapist') {
            // For independent therapist: check specific date availability
            $isValid = $therapist->availability()
                ->where('is_active', true)
                ->whereNotNull('date') // Therapist schedules have specific dates
                ->where('date', $validated['appointment_date'])
                ->where('start_time', '<=', $validated['appointment_time'])
                ->where('end_time', '>', $validated['appointment_time'])
                ->exists();
            
            if (!$isValid) {
                return back()->withInput()->with('error', 'Selected time slot is not available.');
            }
        } elseif ($therapist->role === 'clinic' || $therapist->role === 'employee') {
            // For clinic or clinic employee: check clinic's weekly schedule
            $statusProvider = ($therapist->role === 'employee') 
                ? $therapist->clinic 
                : $therapist;

            if (!$statusProvider) {
                return back()->withInput()->with('error', 'Service provider information is incomplete.');
            }

            $appointmentDate = Carbon::parse($validated['appointment_date']);
            $dayOfWeek = $appointmentDate->dayOfWeek;
            
            $isValid = $statusProvider->availability()
                ->where('is_active', true)
                ->whereNotNull('day_of_week') // Clinic schedules have day_of_week
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $validated['appointment_time'])
                ->where('end_time', '>', $validated['appointment_time'])
                ->exists();
            
            if (!$isValid) {
                $msg = ($therapist->role === 'employee') 
                    ? 'Selected time slot is not available for this clinic employee.'
                    : 'Selected time slot is not available for this clinic.';
                return back()->withInput()->with('error', $msg);
            }
        }

        // Handle referral file upload before transaction
        $referralPath = null;
        if ($request->hasFile('referral')) {
            $referralPath = $request->file('referral')->store('referrals', 'local');
        }

        // Prevent double-booking atomically using a DB transaction + exclusive row lock.
        // Without a lock, two concurrent requests could both pass the "exists" check
        // and both create an appointment for the same slot (race condition).
        $slotTaken = false;
        DB::transaction(function () use ($therapist, $validated, $referralPath, &$slotTaken) {
            $isTaken = Appointment::where('provider_id', $therapist->id)
                ->where('provider_type', get_class($therapist))
                ->where('appointment_date', $validated['appointment_date'])
                ->where('appointment_time', $validated['appointment_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()  // Exclusive lock prevents concurrent inserts
                ->exists();

            if ($isTaken) {
                $slotTaken = true;
                return;
            }

            // Create appointment inside the lock
            Appointment::create([
                'therapist_id' => $therapist->id,
                'provider_id' => $therapist->id,
                'provider_type' => get_class($therapist),
                'appointment_type' => $validated['appointment_type'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'notes' => $validated['notes'] ?? null,
                'referral' => $referralPath,
                'patient_id' => auth()->id(),
                'status' => 'pending',
            ]);
        });

        if ($slotTaken) {
            return back()->withInput()->with('error', 'This time slot is already booked. Please choose another one.');
        }

        // Notify the therapist about new appointment request
        $this->broadcastAppointmentNotification($therapist->id);

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment request submitted successfully!');
    }

    /** ---------------- CANCEL APPOINTMENT ---------------- */
    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', auth()->id()) 
            ->firstOrFail();

        if ($appointment->status !== 'pending') {
            return redirect()->back()->with('error', 'This appointment cannot be canceled.');
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment has been canceled successfully.');
    }

    /**
     * Broadcast appointment notification to a user
     */
    private function broadcastAppointmentNotification($userId)
    {
        $appointmentCount = Appointment::where('provider_id', $userId)
            ->where('status', 'pending')
            ->whereDate('appointment_date', '>=', now())
            ->count();
        broadcast(new AppointmentUpdateEvent($userId, $appointmentCount));
    }
}