<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PaymentType;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Seed invoices and their line items for the last 3 months.
     *
     * Covers the Financial Report feature:
     *   - invoices filtered by invoice_date range
     *   - items with unit_price, discount_pct, quantity
     *   - grouped by payment_type
     */
    public function run(): void
    {
        $patients     = Patient::pluck('id')->toArray();
        $paymentTypes = PaymentType::pluck('id')->toArray();
        $cashiers     = User::where('role', 'cashier')->pluck('id')->toArray();
        $services     = Service::pluck('id', 'name')->toArray(); // name => id

        if (empty($patients)) {
            $this->command->warn('InvoiceSeeder: No patients found — run PatientSeeder first.');
            return;
        }

        if (empty($paymentTypes)) {
            $this->command->warn('InvoiceSeeder: No payment types found — run PaymentTypeSeeder first.');
            return;
        }

        if (empty($cashiers)) {
            // Fall back to any user
            $cashiers = User::pluck('id')->toArray();
        }

        // Weighted payment-type distribution (Cash is most common)
        $paymentWeight = [];
        foreach ($paymentTypes as $ptId) {
            $paymentWeight[] = $ptId;
            $paymentWeight[] = $ptId;   // duplicate to increase weight equally; Cash gets 3× below
        }
        // Add extra weight for the first payment type (Cash)
        array_push($paymentWeight, $paymentTypes[0], $paymentTypes[0]);

        // Service catalogue for realistic invoice items
        $serviceItems = [
            ['name' => 'General Consultation',      'price' => 10.00],
            ['name' => 'Specialist Consultation',   'price' => 20.00],
            ['name' => 'Follow-up Consultation',    'price' =>  7.00],
            ['name' => 'Emergency Consultation',    'price' => 25.00],
            ['name' => 'Complete Blood Count (CBC)','price' =>  8.00],
            ['name' => 'Blood Sugar (Glucose)',     'price' =>  4.00],
            ['name' => 'Liver Function Test (LFT)', 'price' => 12.00],
            ['name' => 'Urine Analysis',            'price' =>  4.00],
            ['name' => 'Malaria Test (RDT)',        'price' =>  3.00],
            ['name' => 'Chest X-Ray',               'price' => 15.00],
            ['name' => 'Abdominal Ultrasound',      'price' => 20.00],
            ['name' => 'IV Drip Setup',             'price' =>  5.00],
            ['name' => 'Wound Dressing',            'price' =>  3.00],
            ['name' => 'Injection (IM/IV)',         'price' =>  2.00],
            ['name' => 'General Ward (per day)',    'price' => 20.00],
            ['name' => 'Private Room (per day)',    'price' => 50.00],
            ['name' => 'Admission Fee',             'price' => 10.00],
            ['name' => 'Prescription Dispensing',  'price' =>  2.00],
            ['name' => 'Nebulisation',              'price' =>  5.00],
            ['name' => 'ECG',                       'price' =>  8.00],
        ];

        $wards = ['General Ward', 'Private Room', 'ICU', null, null, null]; // mostly null (OPD)

        $today   = Carbon::today();
        $created = 0;

        // 45 invoices spread over the last 3 months
        for ($i = 0; $i < 45; $i++) {
            $invoiceDate = $today->copy()->subDays(rand(0, 90));
            $patientId   = $patients[array_rand($patients)];
            $cashierId   = $cashiers[array_rand($cashiers)];
            $paymentId   = $paymentWeight[array_rand($paymentWeight)];

            // Link to an existing visit for the same patient ~60% of the time
            $visitId = null;
            if (rand(1, 10) <= 6) {
                $visit = PatientVisit::where('patient_id', $patientId)
                    ->whereDate('visit_date', '<=', $invoiceDate->toDateString())
                    ->inRandomOrder()
                    ->first();
                $visitId = $visit?->id;
            }

            $invoiceNumber = 'INV-' . strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));

            $invoice = Invoice::create([
                'patient_id'       => $patientId,
                'patient_visit_id' => $visitId,
                'invoice_number'   => $invoiceNumber,
                'payment_type_id'  => $paymentId,
                'ward'             => $wards[array_rand($wards)],
                'remark'           => null,
                'cashier_id'       => $cashierId,
                'invoice_date'     => $invoiceDate->toDateString(),
                'money_paid'       => 0, // calculated after items
            ]);

            // 1–4 line items per invoice
            $itemCount     = rand(1, 4);
            $selectedItems = (array) array_rand($serviceItems, min($itemCount, count($serviceItems)));
            $totalPayable  = 0.0;

            foreach ($selectedItems as $idx) {
                $svc      = $serviceItems[$idx];
                $qty      = rand(1, 3);
                $discount = (rand(1, 10) <= 2) ? round(rand(5, 20), 0) : 0; // 20% chance of discount

                // Resolve service_id if the service exists in DB
                $serviceId = $services[$svc['name']] ?? null;

                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'service_id'   => $serviceId,
                    'service_name' => $svc['name'],
                    'quantity'     => $qty,
                    'unit_price'   => $svc['price'],
                    'discount_pct' => $discount,
                ]);

                $totalPayable += ($svc['price'] * $qty) * (1 - $discount / 100);
            }

            // money_paid: 80% fully paid, 20% partial
            $moneyPaid = (rand(1, 10) <= 8)
                ? round($totalPayable, 2)
                : round($totalPayable * (rand(50, 90) / 100), 2);

            $invoice->update(['money_paid' => $moneyPaid]);
            $created++;
        }

        $this->command->info("InvoiceSeeder: {$created} invoices created with line items.");
    }
}
