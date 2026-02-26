<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'is_super',
        'client_id',
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
            'is_super'          => 'boolean',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /** True when this user is the platform-level super admin. */
    public function isSuper(): bool
    {
        return (bool) $this->is_super;
    }

    /** True when this user has the system_admin role (and is not a super admin). */
    public function isSystemAdmin(): bool
    {
        return $this->role === 'system_admin' && !$this->is_super;
    }

    /** True when this user can access elevated analytics (system_admin or super_admin). */
    public function hasAnalyticsAccess(): bool
    {
        return $this->is_super || $this->role === 'system_admin';
    }

    /**
     * True when this user is a regular tenant user (doctor / cashier).
     * Super admins and system admins are NOT client users.
     */
    public function isClientUser(): bool
    {
        return !$this->is_super && $this->role !== 'system_admin';
    }

    /**
     * The named route that serves as this user's home / landing page.
     */
    public function homeRoute(): string
    {
        if ($this->isSuper()) {
            return 'admin.index';
        }

        if ($this->isSystemAdmin()) {
            return 'analytics.index';
        }

        return 'patients.index';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Check whether this user's role is allowed to access a client feature.
     *
     * super_admin is a platform-level account with its own admin panel; they
     * have NO access to tenant-facing features (patients, invoices, etc.).
     *
     * system_admin can reach all client features (needed for user management
     * and data oversight within their tenant), even though those items are
     * hidden from their nav — TenantScope enforces per-tenant data isolation.
     */
    public function canAccess(string $feature): bool
    {
        if ($this->is_super) {
            return false;
        }

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
