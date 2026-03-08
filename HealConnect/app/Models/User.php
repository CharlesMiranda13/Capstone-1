<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Models\Appointment;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_PATIENT = 'patient';
    const ROLE_THERAPIST = 'therapist';
    const ROLE_CLINIC = 'clinic';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'verification_code',
        'is_verified_by_admin',
        'status',
        'profile_picture',
        'gender',
        'phone',
        'specialization',
        'address',
        'street',
        'barangay',
        'city',
        'province',
        'region',
        'postal_code',
        'start_year',
        'clinic_type',
        'valid_id_path',
        'license_path',
        'business_permit_path',
        'business_permit_expiry',
        'clinic_id',
        'position',
        'description',
        'dob',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_started_at' => 'datetime',
            'dob'=> 'date',
            'business_permit_expiry' => 'date',
        ];
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }
    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope a query to filter by region
     */
    public function scopeInRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /** ---------------- SCOPES ---------------- */
    public function scopeVerifiedTherapists($query)
    {
        return $query->whereIn('role', [self::ROLE_THERAPIST, self::ROLE_CLINIC])
                     ->where('is_verified_by_admin', true)
                     ->where('status', 'Active')
                     ->where(function($q) {
                         $q->whereNull('business_permit_expiry')
                           ->orWhere('business_permit_expiry', '>=', now()->toDateString());
                     });
    }

    /**
     * Scope for therapists/clinics who are either subscribed or in valid trial.
     */
    public function scopeSubscribedOrTrial($query)
    {
        return $query->where(function($q) {
            $q->where('subscription_status', 'active')
              ->orWhere(function($q2) {
                  $q2->where('subscription_status', 'inactive')
                     ->where(function($q3) {
                         $q3->selectRaw('count(distinct patient_id)')
                            ->from('appointments')
                            ->where(function($q4) {
                                // Match the provider themselves OR any of their employees
                                $q4->whereColumn('provider_id', 'users.id')
                                   ->orWhereIn('provider_id', function($q5) {
                                       $q5->select('id')
                                          ->from('users as u2')
                                          ->whereColumn('u2.clinic_id', 'users.id');
                                   });
                            })
                            ->where('status', '!=', 'cancelled');
                     }, '<', 2); // FIX: Must be < 2 to be shown, because 2 is the maximum limit on the free trial
              });
        });
    }

    /** ---------------- RELATIONSHIPS ---------------- */
    // Availability (polymorphic)
    public function availability()
    {
        return $this->morphMany(\App\Models\Availability::class, 'provider');
    }

    // Clinic Employees
    public function employees()
    {
        return $this->hasMany(User::class, 'clinic_id');
    }

    // Employee's Clinic
    public function clinic()
    {
        return $this->belongsTo(User::class, 'clinic_id');
    }

    public function activeAvailability()
    {
        return $this->morphMany(\App\Models\Availability::class, 'provider')
                    ->where('is_active', true);
    }

    // Services (polymorphic)
    public function services()
    {
        return $this->morphMany(\App\Models\TherapistService::class, 'serviceable');
    }

    // Appointments where user is the provider
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'provider');
    }

    // Appointments where user is the patient
    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // Messaging
    public function sender()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id');
    }

    /** ---------------- EXPIRATION HELPERS ---------------- */
    public function isBusinessPermitExpired()
    {
        if (!$this->business_permit_expiry) return false;
        return $this->business_permit_expiry->isPast() && !$this->business_permit_expiry->isToday();
    }

    public function isBusinessPermitExpiringSoon()
    {
        if (!$this->business_permit_expiry) return false;
        if ($this->isBusinessPermitExpired()) return false;
        
        $daysUntilExpiry = now()->diffInDays($this->business_permit_expiry, false);
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
    }

    /** ---------------- ACCESSORS ---------------- */
    public function getExperienceYearsAttribute()
    {
        if (!$this->start_year) {
            return null;
        }

        return Carbon::now()->year - (int) $this->start_year;
    }

    public function getRoleDisplayAttribute()
    {
        return match($this->role) {
            'therapist' => 'Independent Therapist', 
            'clinic' => 'Clinic',
            'patient' => 'Patient',
            default => ucfirst($this->role),
        };
    }
    public function getFormattedDobAttribute()
    {
        // Check if dob is empty, null, or invalid date
        if (empty($this->dob) || $this->dob === '0000-00-00') {
            return 'Not provided';
        }

        try {
            $date = $this->dob instanceof \Carbon\Carbon 
                ? $this->dob 
                : \Carbon\Carbon::parse($this->dob);
            
            return $date->format('F j, Y');
        } catch (\Exception $e) {
            return 'Not provided';
        }
    }

    /** ---------------- HELPERS ---------------- */
    /**
     * Get upcoming availability for the therapist or clinic.
     * For clinics: expand weekly schedule to upcoming dates.
     * For independent therapists: date-specific availability.
     *
     * @param int $weeks Number of weeks to expand for clinics
     * @return \Illuminate\Support\Collection
     */
    public function upcomingAvailability($weeks = 2)
    {
        if ($this->role === self::ROLE_CLINIC) {
            // Clinic weekly schedule
            $dates = [];
            $schedule = $this->availability()->where('is_active', true)->get();
            $period = [];
            for ($i = 0; $i < $weeks * 7; $i++) {
                $period[] = Carbon::today()->addDays($i);
            }

            foreach ($period as $day) {
                foreach ($schedule as $slot) {
                    if ($day->dayOfWeek == (int)$slot->day_of_week) {
                        $dates[] = [
                            'date' => $day->toDateString(),
                            'start_time' => $slot->start_time,
                            'end_time' => $slot->end_time,
                            'is_active' => $slot->is_active,
                        ];
                    }
                }
            }

            return collect($dates)->filter(function($slot) {
                return !Carbon::parse($slot['date'])->isPast();
            })->values();

        } else {
            // Independent therapist: date-specific availability
            return $this->availability()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereDate('date', '>', Carbon::today()) // future dates
                    ->orWhere(function ($q2) {
                        $q2->whereDate('date', Carbon::today())
                            ->where('start_time', '>=', Carbon::now()->format('H:i:s')); 
                    });
                })
                ->orderBy('date')
                ->orderBy('start_time')
                ->get(['date', 'start_time', 'end_time', 'is_active']);
        }
    }

    /**
     * Count unique customers (patients) for this provider (or collective for clinics).
     */
    public function getCustomerCountAttribute()
    {
        $query = Appointment::where('status', '!=', 'cancelled');

        if ($this->role === 'clinic') {
            // Count for clinic and all its employees
            $employeeIds = $this->employees()->pluck('id')->push($this->id);
            $query->whereIn('provider_id', $employeeIds);
        } else {
            // Count for solo therapist or specific employee
            $query->where('provider_id', $this->id);
        }

        return $query->distinct('patient_id')->count('patient_id');
    }

    /**
     * Check if the user is in trial mode and can access the system.
     */
    public function canAccessSystem()
    {
        // If employee, check their clinic's subscription and permit status
        if ($this->role === 'employee' && $this->clinic_id) {
            $clinic = $this->clinic;
            return $clinic ? $clinic->canAccessSystem() : false;
        }

        // Admin is not subject to these rules
        if ($this->role === 'admin') {
            return true;
        }

        // Check Business Permit Expiry first (for clinics/independent therapists)
        if (in_array($this->role, [self::ROLE_CLINIC, self::ROLE_THERAPIST])) {
            if ($this->isBusinessPermitExpired()) {
                return false;
            }
        }

        // Patient is not subject to subscription rules
        if ($this->role === 'patient') {
            return true;
        }

        if ($this->subscription_status === 'active') {
            return true;
        }

        if ($this->subscription_status === 'expired') {
            return false;
        }

        // inactive (trial)
        if ($this->subscription_status === 'inactive') {
            return $this->customer_count < 3;
        }
        return false;
    }

    /**
     * Check if this therapist/clinic has an active (pending/confirmed) appointment today with a patient.
     */
    public function hasActiveAppointmentToday(User $patient)
    {
        // Clinics check for themselves or any of their employees
        $providerIds = [$this->id];
        if ($this->role === self::ROLE_CLINIC) {
            $providerIds = $this->employees()->pluck('id')->push($this->id)->toArray();
        }

        return Appointment::whereIn('status', ['pending', 'approved'])
            ->whereDate('appointment_date', \Carbon\Carbon::now('Asia/Manila')->toDateString())
            ->where('patient_id', $patient->id)
            ->whereIn('provider_id', $providerIds)
            ->exists();
    }
}
