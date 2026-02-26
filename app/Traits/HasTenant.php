<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

/**
 * Apply this trait to any model that holds a tenant_id column.
 *
 * It does two things automatically:
 *  1. Adds TenantScope as a global query scope so all reads/updates/deletes
 *     are automatically filtered to the authenticated user's tenant.
 *  2. Stamps tenant_id on every new record from the current user's client_id,
 *     so inserts never land in the wrong tenant by accident.
 *
 * Super admins bypass the scope (they see all tenants) and their inserts
 * get tenant_id = null unless they set it explicitly.
 */
trait HasTenant
{
    protected static function bootHasTenant(): void
    {
        // ── 1. Global query scope ─────────────────────────────────
        static::addGlobalScope(new TenantScope());

        // ── 2. Auto-stamp tenant_id on insert ─────────────────────
        static::creating(function (Model $model) {
            // Already set explicitly — respect it.
            if ($model->tenant_id !== null) {
                return;
            }

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            // Super admin: leave null (they create platform-level records
            // unless they explicitly pass a tenant_id).
            if ($user->isSuper()) {
                return;
            }

            // Regular user: stamp their client's tenant.
            if ($user->client_id) {
                $model->tenant_id = $user->client_id;
            }
        });
    }
}
