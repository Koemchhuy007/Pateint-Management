<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'surname',
        'given_name',
        'date_of_birth',
        'sex',
        'personal_status',
        'email',
        'phone',
        'address',
        'province_id',
        'district_id',
        'community_id',
        'village_id',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_notes',
        'insurance_info',
        'status',
        'active_case',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'active_case'   => 'boolean',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class)->latest('visit_date');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : null;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->given_name} {$this->surname}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->village?->name,
            $this->community?->name,
            $this->district?->name,
            $this->province?->name,
        ]);
        $base = implode(', ', $parts);
        return $this->address ? "{$this->address}, {$base}" : $base;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('patient_id', 'like', "%{$search}%")
                ->orWhere('given_name', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
