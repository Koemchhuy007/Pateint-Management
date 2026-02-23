<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // system_admin gets everything (handled in code, no DB rows needed)
        // but we seed defaults for doctor and cashier

        $defaults = [
            'doctor' => [
                'patients',
                'drugstore',
            ],
            'cashier' => [
                'patients',
                'invoice',
            ],
        ];

        foreach ($defaults as $role => $features) {
            foreach ($features as $feature) {
                RolePermission::firstOrCreate([
                    'role'    => $role,
                    'feature' => $feature,
                ]);
            }
        }
    }
}
