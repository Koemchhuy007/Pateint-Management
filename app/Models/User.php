<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLES = [
        'system_admin' => 'System Admin',
        'doctor'       => 'Doctor',
        'cashier'      => 'Cashier',
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Check whether this user's role is allowed to access a feature.
     * Results are cached per-request via a static array.
     */
    public function canAccess(string $feature): bool
    {
        if ($this->role === 'system_admin') {
            return true;
        }

        static $cache = [];

        $key = $this->role . ':' . $feature;

        if (!array_key_exists($key, $cache)) {
            $cache[$key] = RolePermission::where('role', $this->role)
                ->where('feature', $feature)
                ->exists();
        }

        return $cache[$key];
    }
}
