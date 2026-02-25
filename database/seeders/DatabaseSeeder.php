<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SystemAdminSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
            PatientSeeder::class,
            DrugstoreSeeder::class,
            PaymentTypeSeeder::class,
            ServiceGroupSeeder::class,
            RolePermissionSeeder::class,
            // Report feature seeders (depend on the above)
            PatientVisitSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
