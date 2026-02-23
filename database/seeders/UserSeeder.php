<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Dr. Sophea Chan',
                'username' => 'dr_sophea',
                'email'    => 'sophea@clinic.local',
                'password' => Hash::make('password'),
                'role'     => 'doctor',
            ],
            [
                'name'     => 'Dr. Dara Meas',
                'username' => 'dr_dara',
                'email'    => 'dara@clinic.local',
                'password' => Hash::make('password'),
                'role'     => 'doctor',
            ],
            [
                'name'     => 'Cashier Bopha',
                'username' => 'cashier_bopha',
                'email'    => 'bopha@clinic.local',
                'password' => Hash::make('password'),
                'role'     => 'cashier',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['username' => $data['username']],
                $data
            );
        }
    }
}
