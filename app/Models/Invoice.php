<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'patient_id',
        'patient_visit_id',
        'invoice_number',
        'payment_type_id',
        'ward',
        'remark',
        'cashier_id',
        'invoice_date',
        'money_paid',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'money_paid'   => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function patientVisit(): BelongsTo
    {
        return $this->belongsTo(PatientVisit::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getActualAmountAttribute(): float
    {
        return $this->items->sum(fn($i) => $i->unit_price * $i->quantity);
    }

    public function getTotalDiscountAttribute(): float
    {
        return $this->items->sum(fn($i) => $i->unit_price * $i->quantity * $i->discount_pct / 100);
    }

    public function getTotalPayAttribute(): float
    {
        return $this->actual_amount - $this->total_discount;
    }

    public static function generateNumber(): string
    {
        return 'INV-' . strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));
    }
}
