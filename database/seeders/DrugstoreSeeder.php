<?php

namespace Database\Seeders;

use App\Models\Drug;
use App\Models\DrugType;
use Illuminate\Database\Seeder;

class DrugstoreSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'name'        => 'Analgesics & Antipyretics',
                'description' => 'Pain relief and fever-reducing medications.',
                'drugs'       => [
                    ['name' => 'Paracetamol 500 mg',      'unit' => 'Tablet',  'stock_quantity' => 500, 'description' => 'Relieves mild to moderate pain and reduces fever.'],
                    ['name' => 'Ibuprofen 400 mg',         'unit' => 'Tablet',  'stock_quantity' => 300, 'description' => 'NSAID for pain, inflammation, and fever.'],
                    ['name' => 'Aspirin 100 mg',           'unit' => 'Tablet',  'stock_quantity' => 200, 'description' => 'Low-dose aspirin for pain relief and antiplatelet use.'],
                    ['name' => 'Diclofenac Sodium 50 mg',  'unit' => 'Tablet',  'stock_quantity' => 150, 'description' => 'NSAID for musculoskeletal pain and inflammation.'],
                    ['name' => 'Paracetamol Syrup 120 mg/5 ml', 'unit' => 'Bottle', 'stock_quantity' => 80, 'description' => 'Paracetamol oral suspension for children.'],
                ],
            ],
            [
                'name'        => 'Antibiotics',
                'description' => 'Medications used to treat bacterial infections.',
                'drugs'       => [
                    ['name' => 'Amoxicillin 500 mg',         'unit' => 'Capsule', 'stock_quantity' => 400, 'description' => 'Broad-spectrum penicillin antibiotic.'],
                    ['name' => 'Azithromycin 500 mg',        'unit' => 'Tablet',  'stock_quantity' => 200, 'description' => 'Macrolide antibiotic for respiratory and skin infections.'],
                    ['name' => 'Ciprofloxacin 500 mg',       'unit' => 'Tablet',  'stock_quantity' => 180, 'description' => 'Fluoroquinolone for urinary tract and GI infections.'],
                    ['name' => 'Metronidazole 400 mg',       'unit' => 'Tablet',  'stock_quantity' => 150, 'description' => 'Antibiotic and antiprotozoal for anaerobic infections.'],
                    ['name' => 'Doxycycline 100 mg',         'unit' => 'Capsule', 'stock_quantity' => 120, 'description' => 'Tetracycline antibiotic for a wide range of infections.'],
                    ['name' => 'Cloxacillin 500 mg',         'unit' => 'Capsule', 'stock_quantity' => 100, 'description' => 'Penicillinase-resistant antibiotic for staphylococcal infections.'],
                ],
            ],
            [
                'name'        => 'Antihypertensives',
                'description' => 'Medications used to manage high blood pressure.',
                'drugs'       => [
                    ['name' => 'Amlodipine 5 mg',          'unit' => 'Tablet', 'stock_quantity' => 350, 'description' => 'Calcium channel blocker for hypertension and angina.'],
                    ['name' => 'Amlodipine 10 mg',         'unit' => 'Tablet', 'stock_quantity' => 200, 'description' => 'Higher-dose calcium channel blocker for hypertension.'],
                    ['name' => 'Lisinopril 10 mg',         'unit' => 'Tablet', 'stock_quantity' => 250, 'description' => 'ACE inhibitor for hypertension and heart failure.'],
                    ['name' => 'Losartan 50 mg',           'unit' => 'Tablet', 'stock_quantity' => 220, 'description' => 'ARB for hypertension and kidney protection in diabetics.'],
                    ['name' => 'Atenolol 50 mg',           'unit' => 'Tablet', 'stock_quantity' => 180, 'description' => 'Beta-blocker for hypertension and angina.'],
                    ['name' => 'Hydrochlorothiazide 25 mg','unit' => 'Tablet', 'stock_quantity' => 160, 'description' => 'Thiazide diuretic for hypertension and edema.'],
                ],
            ],
            [
                'name'        => 'Antidiabetics',
                'description' => 'Medications used to control blood glucose levels.',
                'drugs'       => [
                    ['name' => 'Metformin 500 mg',         'unit' => 'Tablet', 'stock_quantity' => 400, 'description' => 'First-line oral antidiabetic for type 2 diabetes.'],
                    ['name' => 'Metformin 850 mg',         'unit' => 'Tablet', 'stock_quantity' => 250, 'description' => 'Higher-dose metformin for blood glucose control.'],
                    ['name' => 'Glibenclamide 5 mg',       'unit' => 'Tablet', 'stock_quantity' => 200, 'description' => 'Sulfonylurea that stimulates insulin secretion.'],
                    ['name' => 'Glipizide 5 mg',           'unit' => 'Tablet', 'stock_quantity' => 150, 'description' => 'Sulfonylurea for type 2 diabetes management.'],
                    ['name' => 'Insulin Regular 100 IU/ml','unit' => 'Vial',   'stock_quantity' => 50,  'description' => 'Short-acting insulin for type 1 and type 2 diabetes.'],
                ],
            ],
            [
                'name'        => 'Antihistamines',
                'description' => 'Medications to relieve allergy symptoms.',
                'drugs'       => [
                    ['name' => 'Cetirizine 10 mg',         'unit' => 'Tablet',  'stock_quantity' => 300, 'description' => 'Second-generation antihistamine for allergic rhinitis and urticaria.'],
                    ['name' => 'Loratadine 10 mg',         'unit' => 'Tablet',  'stock_quantity' => 250, 'description' => 'Non-sedating antihistamine for allergies.'],
                    ['name' => 'Chlorphenamine 4 mg',      'unit' => 'Tablet',  'stock_quantity' => 200, 'description' => 'First-generation antihistamine for allergy and cold symptoms.'],
                    ['name' => 'Diphenhydramine 25 mg',    'unit' => 'Capsule', 'stock_quantity' => 120, 'description' => 'Antihistamine also used as a sleep aid.'],
                ],
            ],
            [
                'name'        => 'Gastrointestinal',
                'description' => 'Medications for digestive system conditions.',
                'drugs'       => [
                    ['name' => 'Omeprazole 20 mg',         'unit' => 'Capsule', 'stock_quantity' => 350, 'description' => 'Proton pump inhibitor for peptic ulcer and GERD.'],
                    ['name' => 'Ranitidine 150 mg',        'unit' => 'Tablet',  'stock_quantity' => 200, 'description' => 'H2 blocker for acid reflux and gastric ulcer.'],
                    ['name' => 'Domperidone 10 mg',        'unit' => 'Tablet',  'stock_quantity' => 180, 'description' => 'Antiemetic and prokinetic for nausea and vomiting.'],
                    ['name' => 'Loperamide 2 mg',          'unit' => 'Capsule', 'stock_quantity' => 150, 'description' => 'Antidiarrheal for acute and chronic diarrhea.'],
                    ['name' => 'Lactulose 10 g/15 ml',     'unit' => 'Bottle',  'stock_quantity' => 60,  'description' => 'Osmotic laxative for constipation and hepatic encephalopathy.'],
                    ['name' => 'Antacid Suspension',       'unit' => 'Bottle',  'stock_quantity' => 80,  'description' => 'Aluminum/magnesium hydroxide for heartburn and indigestion.'],
                ],
            ],
            [
                'name'        => 'Vitamins & Supplements',
                'description' => 'Nutritional supplements and vitamin preparations.',
                'drugs'       => [
                    ['name' => 'Vitamin C 500 mg',         'unit' => 'Tablet',  'stock_quantity' => 500, 'description' => 'Ascorbic acid supplement for immune support.'],
                    ['name' => 'Vitamin B Complex',        'unit' => 'Tablet',  'stock_quantity' => 400, 'description' => 'Combined B vitamins for energy metabolism and nerve function.'],
                    ['name' => 'Folic Acid 5 mg',          'unit' => 'Tablet',  'stock_quantity' => 300, 'description' => 'Folate supplement for anemia prevention and pregnancy.'],
                    ['name' => 'Ferrous Sulfate 200 mg',   'unit' => 'Tablet',  'stock_quantity' => 250, 'description' => 'Iron supplement for iron-deficiency anemia.'],
                    ['name' => 'Calcium Carbonate 500 mg', 'unit' => 'Tablet',  'stock_quantity' => 200, 'description' => 'Calcium supplement for bone health.'],
                    ['name' => 'Vitamin D3 1000 IU',       'unit' => 'Capsule', 'stock_quantity' => 150, 'description' => 'Cholecalciferol for vitamin D deficiency.'],
                ],
            ],
            [
                'name'        => 'Respiratory',
                'description' => 'Medications for respiratory tract conditions.',
                'drugs'       => [
                    ['name' => 'Salbutamol 2 mg',              'unit' => 'Tablet',  'stock_quantity' => 250, 'description' => 'Short-acting beta-agonist for asthma and bronchospasm.'],
                    ['name' => 'Salbutamol Inhaler 100 mcg',   'unit' => 'Inhaler', 'stock_quantity' => 80,  'description' => 'Pressurised MDI for acute asthma relief.'],
                    ['name' => 'Ambroxol 30 mg',               'unit' => 'Tablet',  'stock_quantity' => 200, 'description' => 'Mucolytic for productive cough and bronchitis.'],
                    ['name' => 'Dextromethorphan 15 mg',       'unit' => 'Tablet',  'stock_quantity' => 150, 'description' => 'Cough suppressant for dry cough.'],
                    ['name' => 'Prednisolone 5 mg',            'unit' => 'Tablet',  'stock_quantity' => 120, 'description' => 'Corticosteroid for severe asthma and inflammatory conditions.'],
                ],
            ],
            [
                'name'        => 'Cardiovascular',
                'description' => 'Medications for heart and circulatory conditions.',
                'drugs'       => [
                    ['name' => 'Atorvastatin 20 mg',     'unit' => 'Tablet', 'stock_quantity' => 300, 'description' => 'Statin for lowering LDL cholesterol and cardiovascular risk.'],
                    ['name' => 'Atorvastatin 40 mg',     'unit' => 'Tablet', 'stock_quantity' => 200, 'description' => 'Higher-dose statin for dyslipidemia.'],
                    ['name' => 'Simvastatin 20 mg',      'unit' => 'Tablet', 'stock_quantity' => 180, 'description' => 'Statin for cholesterol management.'],
                    ['name' => 'Digoxin 0.25 mg',        'unit' => 'Tablet', 'stock_quantity' => 8,   'description' => 'Cardiac glycoside for heart failure and atrial fibrillation.'],
                    ['name' => 'Isosorbide Dinitrate 5 mg','unit' => 'Tablet','stock_quantity' => 100, 'description' => 'Nitrate for angina prophylaxis.'],
                ],
            ],
            [
                'name'        => 'Dermatologicals',
                'description' => 'Topical and systemic medications for skin conditions.',
                'drugs'       => [
                    ['name' => 'Hydrocortisone Cream 1%',    'unit' => 'Tube',   'stock_quantity' => 120, 'description' => 'Mild topical corticosteroid for eczema and dermatitis.'],
                    ['name' => 'Clotrimazole Cream 1%',      'unit' => 'Tube',   'stock_quantity' => 100, 'description' => 'Antifungal cream for tinea and candidal skin infections.'],
                    ['name' => 'Betamethasone Cream 0.1%',   'unit' => 'Tube',   'stock_quantity' => 80,  'description' => 'Potent topical corticosteroid for inflammatory skin conditions.'],
                    ['name' => 'Mupirocin Ointment 2%',      'unit' => 'Tube',   'stock_quantity' => 70,  'description' => 'Topical antibiotic for impetigo and minor skin infections.'],
                    ['name' => 'Calamine Lotion',             'unit' => 'Bottle', 'stock_quantity' => 60,  'description' => 'Soothing lotion for itching, rashes, and insect bites.'],
                ],
            ],
        ];

        foreach ($catalog as $typeData) {
            $type = DrugType::firstOrCreate(
                ['name' => $typeData['name']],
                ['description' => $typeData['description']]
            );

            foreach ($typeData['drugs'] as $drugData) {
                Drug::firstOrCreate(
                    [
                        'drug_type_id' => $type->id,
                        'name'         => $drugData['name'],
                    ],
                    [
                        'unit'          => $drugData['unit'],
                        'stock_quantity'=> $drugData['stock_quantity'],
                        'description'   => $drugData['description'],
                    ]
                );
            }
        }

        $this->command->info('Drugstore seeder completed: ' .
            DrugType::count() . ' drug types, ' .
            Drug::count() . ' drugs.'
        );
    }
}
