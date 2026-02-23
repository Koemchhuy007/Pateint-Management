<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Cash',             'description' => 'Payment made in cash'],
            ['name' => 'ABA Bank',         'description' => 'ABA Bank transfer or KHQR'],
            ['name' => 'ACLEDA Bank',      'description' => 'ACLEDA Bank transfer or KHQR'],
            ['name' => 'Wing',             'description' => 'Wing mobile money payment'],
            ['name' => 'TrueMoney',        'description' => 'TrueMoney wallet payment'],
            ['name' => 'Health Insurance', 'description' => 'National or private health insurance'],
            ['name' => 'NSSF',             'description' => 'National Social Security Fund'],
            ['name' => 'Credit Card',      'description' => 'Visa / Mastercard credit card'],
        ];

        foreach ($types as $type) {
            PaymentType::firstOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description'], 'is_active' => true]
            );
        }
    }
}
