<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drug extends Model
{
    use HasTenant;

    protected $fillable = [
        'drug_type_id',
        'name',
        'unit',
        'stock_quantity',
        'description',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
    ];

    public function drugType(): BelongsTo
    {
        return $this->belongsTo(DrugType::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= 10;
    }
}
