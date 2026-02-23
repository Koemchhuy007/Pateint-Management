<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Build a lookup: $permissions['doctor']['patients'] = true
        $rows = RolePermission::all();
        $permissions = [];
        foreach ($rows as $row) {
            $permissions[$row->role][$row->feature] = true;
        }

        $roles    = User::ROLES;
        $features = RolePermission::FEATURES;

        return view('settings.role-permissions.index', compact('permissions', 'roles', 'features'));
    }

    public function update(Request $request)
    {
        $incoming = $request->input('permissions', []);   // ['doctor' => ['patients' => '1', ...], ...]
        $features = array_keys(RolePermission::FEATURES);
        $roles    = array_keys(User::ROLES);

        foreach ($roles as $role) {
            // system_admin is always full access — skip
            if ($role === 'system_admin') continue;

            foreach ($features as $feature) {
                $granted = !empty($incoming[$role][$feature]);

                if ($granted) {
                    RolePermission::firstOrCreate(['role' => $role, 'feature' => $feature]);
                } else {
                    RolePermission::where('role', $role)->where('feature', $feature)->delete();
                }
            }
        }

        return redirect()->route('settings.role-permissions.index')
            ->with('success', 'Role permissions updated successfully.');
    }
}
