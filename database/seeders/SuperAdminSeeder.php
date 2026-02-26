<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = [
            'username' => 'superadmin',
            'email'    => 'superadmin@platform.local',
        ];

        $data = [
            'name'     => 'Super Administrator',
            'password' => Hash::make('SuperAdmin@2026'),
            'role'     => 'system_admin',
            'is_super' => true,
            'client_id'=> null,
        ];

        $user = User::updateOrCreate($credentials, $data);

        $this->command->info('Super admin ready.');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Username', $user->username],
                ['Email',    $user->email],
                ['Password', 'SuperAdmin@2026'],
                ['Is Super', 'Yes'],
            ]
        );
    }
}
