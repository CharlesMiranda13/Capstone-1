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
        'notes',
        'status',
    ];

    /** ---------------- RELATIONSHIPS ---------------- */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    //either IndependetTherapist or Clinic
    public function provider()
    {
        return $this->morphTo();
    }

    public function scopeForProvider($query, $provider)
    {
        return $query->where('provider_id', $provider->id)
                     ->where('provider_type', get_class($provider));
    }
}
