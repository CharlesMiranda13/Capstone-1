<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'date',
        'is_active'
    ];

    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'therapist_id', 'therapist_id')
            ->whereDate('appointment_date', $this->date);
    }
}
