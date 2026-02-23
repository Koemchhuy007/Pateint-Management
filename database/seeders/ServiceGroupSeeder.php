<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceGroup;
use Illuminate\Database\Seeder;

class ServiceGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name'        => 'Consultation',
                'description' => 'Doctor consultation and examination fees',
                'services'    => [
                    ['name' => 'General Consultation',       'price' => 10.00],
                    ['name' => 'Specialist Consultation',    'price' => 20.00],
                    ['name' => 'Follow-up Consultation',     'price' => 7.00],
                    ['name' => 'Emergency Consultation',     'price' => 25.00],
                ],
            ],
            [
                'name'        => 'Laboratory',
                'description' => 'Blood tests and other lab examinations',
                'services'    => [
                    ['name' => 'Complete Blood Count (CBC)', 'price' => 8.00],
                    ['name' => 'Blood Sugar (Glucose)',      'price' => 4.00],
                    ['name' => 'Liver Function Test (LFT)',  'price' => 12.00],
                    ['name' => 'Kidney Function Test',       'price' => 12.00],
                    ['name' => 'Urine Analysis',             'price' => 4.00],
                    ['name' => 'HIV Test',                   'price' => 5.00],
                    ['name' => 'Malaria Test (RDT)',         'price' => 3.00],
                    ['name' => 'Pregnancy Test',             'price' => 3.00],
                ],
            ],
            [
                'name'        => 'Radiology',
                'description' => 'Imaging and radiology services',
                'services'    => [
                    ['name' => 'Chest X-Ray',                'price' => 15.00],
                    ['name' => 'Abdominal Ultrasound',       'price' => 20.00],
                    ['name' => 'Pelvic Ultrasound',          'price' => 20.00],
                    ['name' => 'Obstetric Ultrasound',       'price' => 20.00],
                    ['name' => 'CT Scan (Head)',              'price' => 80.00],
                    ['name' => 'CT Scan (Abdomen)',          'price' => 90.00],
                ],
            ],
            [
                'name'        => 'Nursing & Procedures',
                'description' => 'Nursing care and minor medical procedures',
                'services'    => [
                    ['name' => 'IV Drip Setup',              'price' => 5.00],
                    ['name' => 'Wound Dressing',             'price' => 3.00],
                    ['name' => 'Injection (IM/IV)',          'price' => 2.00],
                    ['name' => 'Nebulisation',               'price' => 5.00],
                    ['name' => 'Blood Pressure Monitoring',  'price' => 1.00],
                    ['name' => 'ECG',                        'price' => 8.00],
                ],
            ],
            [
                'name'        => 'Pharmacy',
                'description' => 'Dispensed medications and pharmacy services',
                'services'    => [
                    ['name' => 'Prescription Dispensing',   'price' => 2.00],
                ],
            ],
            [
                'name'        => 'Admission & Room',
                'description' => 'Inpatient admission and room charges',
                'services'    => [
                    ['name' => 'General Ward (per day)',     'price' => 20.00],
                    ['name' => 'Private Room (per day)',     'price' => 50.00],
                    ['name' => 'ICU (per day)',              'price' => 150.00],
                    ['name' => 'Admission Fee',              'price' => 10.00],
                ],
            ],
        ];

        foreach ($groups as $groupData) {
            $services = $groupData['services'];
            unset($groupData['services']);

            $group = ServiceGroup::firstOrCreate(
                ['name' => $groupData['name']],
                ['description' => $groupData['description']]
            );

            foreach ($services as $svc) {
                Service::firstOrCreate(
                    [
                        'service_group_id' => $group->id,
                        'name'             => $svc['name'],
                    ],
                    [
                        'price'       => $svc['price'],
                        'description' => null,
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}
