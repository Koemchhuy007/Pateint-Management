<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DrugType extends Model
{
    protected $fillable = ['name', 'description'];

    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }
}
