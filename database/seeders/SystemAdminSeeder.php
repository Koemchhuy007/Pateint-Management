<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = [
            'username' => 'admin123',
            'email'    => 'admin123@clinic.local',
        ];

        $data = [
            'name'     => 'System Administrator',
            'password' => Hash::make('admin123'),
            'role'     => 'system_admin',
        ];

        $admin = User::updateOrCreate($credentials, $data);

        $this->command->info('System admin ready.');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Username', $admin->username],
                ['Email',    $admin->email],
                ['Password', 'Admin1234'],
                ['Role',     $admin->role],
            ]
        );
    }
}
