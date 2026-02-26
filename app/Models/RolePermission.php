<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasTenant;

    protected $fillable = ['role', 'feature'];

    /** All configurable nav features */
    const FEATURES = [
        'patients'  => 'Patients',
        'invoice'   => 'Invoice',
        'drugstore' => 'Drugstore',
        'reports'   => 'Reports',
        'settings'  => 'Settings',
    ];

    /**
     * Return a flat set of features allowed for a given role.
     * System admin always gets everything.
     */
    public static function allowedFor(string $role): array
    {
        if ($role === 'system_admin') {
            return array_keys(self::FEATURES);
        }

        return static::where('role', $role)
            ->pluck('feature')
            ->toArray();
    }
}
