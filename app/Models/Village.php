<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    public $timestamps = false;

    protected $fillable = ['community_id', 'name', 'code'];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }
}
