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
        'start_year',
        'valid_id_path',
        'license_path',
        'clinic_id',
        'position',
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
        ];
    }

    /** ---------------- SCOPES ---------------- */
    public function scopeVerifiedTherapists($query)
    {
        return $query->whereIn('role', [self::ROLE_THERAPIST, self::ROLE_CLINIC])
                     ->where('is_verified_by_admin', true)
                     ->where('status', 'Active');
    }

    /** ---------------- RELATIONSHIPS ---------------- */
    // Availability (polymorphic)
    public function availability()
    {
        return $this->morphMany(\App\Models\Availability::class, 'provider');
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
        return $this->hasMany(Appointment::class, 'provider_id');
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

    /** ---------------- ACCESSORS ---------------- */
    public function getExperienceYearsAttribute()
    {
        if (!$this->start_year) {
            return null;
        }

        return Carbon::parse($this->start_year)->diffInYears(Carbon::now());
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
                ->where(function ($query) {
                    $query->whereDate('date', '>=', Carbon::today());
                })
                ->orderBy('date')
                ->get(['date', 'start_time', 'end_time', 'is_active']);
        }
    }
}
