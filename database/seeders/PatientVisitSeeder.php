<?php

namespace Database\Seeders;

use App\Models\Drug;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PatientVisitSeeder extends Seeder
{
    /**
     * Seed patient visits for the last 3 months.
     *
     * Covers two report features:
     *   - Patient Visits Report  (visit_type, discharge_date, visit_date)
     *   - Drug Usage Report      (prescription JSON column)
     */
    public function run(): void
    {
        $patients = Patient::pluck('id')->toArray();
        $doctors  = User::where('role', 'doctor')->pluck('name')->toArray();

        if (empty($patients)) {
            $this->command->warn('PatientVisitSeeder: No patients found — run PatientSeeder first.');
            return;
        }

        if (empty($doctors)) {
            $doctors = ['Dr. Sophea Chan', 'Dr. Dara Meas'];
        }

        // Load drugs for realistic prescriptions
        $drugs = Drug::select('id', 'name', 'unit')->get()->toArray();

        $reasons = [
            'Fever and headache',
            'Abdominal pain',
            'Chest pain and shortness of breath',
            'Skin rash and itching',
            'Persistent cough',
            'Dizziness and nausea',
            'Routine check-up',
            'Hypertension follow-up',
            'Diabetic review',
            'Wound dressing change',
            'Joint pain',
            'Sore throat and runny nose',
            'Eye irritation',
            'Back pain',
            'Urinary tract symptoms',
        ];

        $diagnoses = [
            'Viral upper respiratory tract infection',
            'Acute gastroenteritis',
            'Essential hypertension',
            'Type 2 diabetes mellitus',
            'Peptic ulcer disease',
            'Pneumonia',
            'Urinary tract infection',
            'Allergic rhinitis',
            'Iron-deficiency anaemia',
            'Lumbago',
            'Acute tonsillitis',
            'Dengue fever',
            'Skin dermatitis',
            'Migraine',
            'Acute otitis media',
        ];

        $treatments = [
            'Symptomatic treatment with analgesics and adequate hydration',
            'Oral antibiotics for 7 days, rest, and fluid intake',
            'Blood pressure medication adjusted; low-sodium diet advised',
            'Insulin dosage reviewed; dietary counselling provided',
            'Proton pump inhibitor course; avoid NSAIDs',
            'IV antibiotics and oxygen therapy',
            'Oral antibiotics; increased fluid intake',
            'Antihistamines prescribed; allergen avoidance counselled',
            'Iron supplementation and dietary advice',
            'Physiotherapy referral; muscle relaxants prescribed',
            'Throat lozenges and antibiotics if bacterial',
            'Supportive care; platelet monitoring',
            'Topical corticosteroid cream',
            'Triptans prescribed; trigger diary recommended',
            'Ear drops and analgesics',
        ];

        $wards = ['General Ward', 'Private Room', 'ICU', null];

        // Generate 60 visits spread over the last 3 months
        $visits = [];
        $today  = Carbon::today();

        for ($i = 0; $i < 60; $i++) {
            $visitDate = $today->copy()->subDays(rand(0, 90))->setTime(rand(7, 17), rand(0, 59));
            $visitType = (rand(1, 10) <= 7) ? 'OPD' : 'IPD';  // 70% OPD, 30% IPD

            // Discharge: only IPD can be discharged
            $dischargeDate = null;
            if ($visitType === 'IPD') {
                if (rand(1, 10) <= 6) {  // 60% of IPD are discharged
                    $stayDays      = rand(1, 7);
                    $dischargeDate = $visitDate->copy()->addDays($stayDays)->toDateString();
                    // Ensure discharge is not in the future
                    if (Carbon::parse($dischargeDate)->gt($today)) {
                        $dischargeDate = $today->toDateString();
                    }
                }
            }

            $diagnosisIndex = rand(0, count($diagnoses) - 1);
            $patientId      = $patients[array_rand($patients)];
            $doctorName     = $doctors[array_rand($doctors)];

            // 45% of visits have a prescription
            $prescription = null;
            if (rand(1, 100) <= 45 && !empty($drugs)) {
                $rxCount      = rand(1, 3);
                $selectedDrugs = array_rand(array_column($drugs, null, 'id'), min($rxCount, count($drugs)));
                if (!is_array($selectedDrugs)) {
                    $selectedDrugs = [$selectedDrugs];
                }

                $prescription = [];
                foreach ($selectedDrugs as $drugId) {
                    $drug = $drugs[array_search($drugId, array_column($drugs, 'id'))];
                    $prescription[] = [
                        'drug_id'    => $drug['id'],
                        'medication' => $drug['name'],
                        'unit'       => $drug['unit'],
                        'quantity'   => rand(1, 3) * 10,
                        'dosage'     => $this->randomDosage(),
                        'duration'   => rand(3, 14) . ' days',
                    ];
                }
            }

            $visits[] = [
                'patient_id'     => $patientId,
                'visit_date'     => $visitDate->toDateTimeString(),
                'visit_type'     => $visitType,
                'reason'         => $reasons[array_rand($reasons)],
                'diagnosis'      => $diagnoses[$diagnosisIndex],
                'treatment'      => $treatments[$diagnosisIndex],
                'doctor_name'    => $doctorName,
                'follow_up_date' => (rand(1, 10) <= 5)
                    ? $visitDate->copy()->addDays(rand(7, 30))->toDateString()
                    : null,
                'discharge_date' => $dischargeDate,
                'notes'          => (rand(1, 10) <= 3)
                    ? 'Patient advised to return if symptoms worsen.'
                    : null,
                'prescription'   => $prescription ? json_encode($prescription) : null,
                'consulting'     => (rand(1, 10) <= 2),
                'created_at'     => $visitDate->toDateTimeString(),
                'updated_at'     => $visitDate->toDateTimeString(),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($visits, 20) as $chunk) {
            PatientVisit::insert($chunk);
        }

        $total   = PatientVisit::count();
        $opd     = PatientVisit::where('visit_type', 'OPD')->count();
        $ipd     = PatientVisit::where('visit_type', 'IPD')->count();
        $rx      = PatientVisit::whereNotNull('prescription')->count();

        $this->command->info("PatientVisitSeeder: {$total} visits ({$opd} OPD, {$ipd} IPD, {$rx} with prescriptions).");
    }

    private function randomDosage(): string
    {
        $dosages = [
            '1 tablet once daily',
            '1 tablet twice daily',
            '1 tablet three times daily',
            '2 tablets twice daily',
            '1 capsule once daily',
            '1 capsule twice daily',
            '5 ml twice daily',
            '10 ml once daily',
            '1 vial daily',
        ];

        return $dosages[array_rand($dosages)];
    }
}
