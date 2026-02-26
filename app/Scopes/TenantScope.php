<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the tenant scope to a given Eloquent query.
     *
     * - Super admins (is_super = true) bypass all tenant filtering.
     * - Regular users with a client_id see only their tenant's rows.
     * - Users without a client_id (legacy / no tenant) see rows where tenant_id IS NULL.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        if ($user->is_super) {
            // Super admin: no tenant restriction
            return;
        }

        if ($user->client_id) {
            $builder->where($model->getTable() . '.tenant_id', $user->client_id);
        } else {
            // Legacy / single-tenant installation: restrict to rows with no tenant
            $builder->whereNull($model->getTable() . '.tenant_id');
        }
    }
}
