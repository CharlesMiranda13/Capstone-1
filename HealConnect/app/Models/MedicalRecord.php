<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'therapist_id',
        'record_date',
        'description',
        'record_type',
        'changed_field',
        'changed_data',
    ];

    protected $casts = [
        'record_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    /**
     * Get the display name for the changed field
     */
    public function getChangedFieldNameAttribute(): string
    {
        return match($this->changed_field) {
            'ehr' => 'Electronic Health Record (EHR)',
            'therapies' => 'Treatment Plan',
            'exercises' => 'Progress Notes',
            default => 'Unknown Field',
        };
    }
}