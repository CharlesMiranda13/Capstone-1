<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',  
        'provider_type',
        'day_of_week',
        'start_time',
        'end_time',
        'date',
        'is_active'
    ];

    public function provider()
    {
        return $this->morphTo();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'provider_id', 'provider_id')
                ->where('provider_type', User::class)
                ->whereDate('appointment_date', $this->date);
    }
}
