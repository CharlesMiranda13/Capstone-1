<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistService extends Model
{
    use HasFactory;

    protected $fillable = [
        'serviceable_id',
        'serviceable_type',
        'appointment_type',
        'description',
        'price',
        'duration'
    ];


    public function serviceable()
    {
        return $this->morphTo();
    }
}
