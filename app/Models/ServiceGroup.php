<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceGroup extends Model
{
    use HasTenant;

    protected $fillable = ['name', 'description'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
