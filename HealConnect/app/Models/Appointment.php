<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'provider_id',
        'provider_type',
        'appointment_type',
        'appointment_date',
        'appointment_time',
        'preferred_gender',
        'notes',
        'status',
    ];

    /** ---------------- RELATIONSHIPS ---------------- */

    // Patient (always a User)
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Provider (either Clinic or Independent Therapist)
    public function provider()
    {
        return $this->morphTo();
    }
    public function therapist()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /** ---------------- SCOPES ---------------- */

    /**
     * Scope to filter appointments for a specific provider.
     * Works for both independent therapists and clinics.
     */
    public function scopeForProvider($query, $provider)
    {
        return $query->where('provider_id', $provider->id)
                     ->where('provider_type', get_class($provider));
    }

}
