<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

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
        'license_path',];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeVerifiedTherapists($query)
    {
        return $query->whereIn('role', ['therapist', 'clinic'])
                ->where('is_verified_by_admin', true)
                ->where('status', 'Active');
    }

    public function availability()
    {
        return $this->morphMany(\App\Models\Availability::class, 'provider');
    }

    public function activeAvailability()
    {
        return $this->morphMany(\App\Models\Availability::class, 'provider')
            ->where('is_active', true);
    }
    public function services()
    {
         return $this->morphMany(\App\Models\TherapistService::class, 'serviceable');
    }
    
    public function getExperienceYearsAttribute()
    {
        if (!$this->start_year) {
            return null;
        }

        return Carbon::parse($this->start_year)->diffInYears(Carbon::now());
    }

    //message
    public function sender()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id');
    }

}
