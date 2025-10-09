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
        'therapist_id',
        'appointment_type',
        'appointment_date',
        'appointment_time',
        'notes',
        'status',
    ];
    
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }
    public function clinic()
    {
        return $this->belongsTo(User::class, 'clinic_id');
    }

}
