<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'service_id',
        'service_name',
        'quantity',
        'unit_price',
        'discount_pct',
    ];

    protected $casts = [
        'unit_price'   => 'decimal:2',
        'discount_pct' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getLineActualAttribute(): float
    {
        return (float) $this->unit_price * $this->quantity;
    }

    public function getLineDiscountAttribute(): float
    {
        return $this->line_actual * (float) $this->discount_pct / 100;
    }

    public function getLineTotalAttribute(): float
    {
        return $this->line_actual - $this->line_discount;
    }
}
