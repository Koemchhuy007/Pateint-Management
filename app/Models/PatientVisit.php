<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientVisit extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'patient_id',
        'visit_date',
        'visit_type',
        'reason',
        'diagnosis',
        'treatment',
        'doctor_name',
        'follow_up_date',
        'discharge_date',
        'notes',
        'prescription',
        'consulting',
    ];

    protected $casts = [
        'visit_date'     => 'datetime',
        'follow_up_date' => 'date',
        'discharge_date' => 'date',
        'prescription'   => 'array',
        'consulting'     => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
