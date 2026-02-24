<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\DrugType;
use App\Models\Invoice;
use App\Models\PatientVisit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Reports dashboard / landing page.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Patient visit report filtered by date range.
     */
    public function patientVisits(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date');
        $endDate   = $request->date('end_date');

        $visits = PatientVisit::with('patient')
            ->whereBetween('visit_date', [
                $startDate->startOfDay()->toDateTimeString(),
                $endDate->copy()->endOfDay()->toDateTimeString(),
            ])
            ->orderBy('visit_date')
            ->get();

        $summary = [
            'total'     => $visits->count(),
            'opd'       => $visits->where('visit_type', 'OPD')->count(),
            'ipd'       => $visits->where('visit_type', 'IPD')->count(),
            'discharged' => $visits->whereNotNull('discharge_date')->count(),
            'active'    => $visits->whereNull('discharge_date')->count(),
        ];

        return view('reports.patient-visits', compact('visits', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Drug usage report filtered by date range (based on prescriptions in visits).
     */
    public function drugUsage(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date');
        $endDate   = $request->date('end_date');

        $visits = PatientVisit::whereBetween('visit_date', [
                $startDate->startOfDay()->toDateTimeString(),
                $endDate->copy()->endOfDay()->toDateTimeString(),
            ])
            ->whereNotNull('prescription')
            ->get(['id', 'patient_id', 'visit_date', 'prescription']);

        // Aggregate drug usage from prescriptions
        $usage = [];
        foreach ($visits as $visit) {
            foreach ($visit->prescription ?? [] as $row) {
                if (empty($row['medication'])) {
                    continue;
                }

                $drugId   = (int) ($row['drug_id'] ?? 0);
                $key      = $drugId > 0 ? 'drug_' . $drugId : 'name_' . $row['medication'];
                $quantity = (int) ($row['quantity'] ?? 0);

                if (!isset($usage[$key])) {
                    $usage[$key] = [
                        'drug_id'    => $drugId > 0 ? $drugId : null,
                        'medication' => $row['medication'],
                        'unit'       => $row['unit'] ?? '',
                        'total_qty'  => 0,
                        'visit_count' => 0,
                    ];
                }

                $usage[$key]['total_qty']   += $quantity;
                $usage[$key]['visit_count'] += 1;
            }
        }

        // Sort by total quantity descending
        usort($usage, fn($a, $b) => $b['total_qty'] <=> $a['total_qty']);

        $visitCount = $visits->count();

        return view('reports.drug-usage', compact('usage', 'visitCount', 'startDate', 'endDate'));
    }

    /**
     * Current drug store stock report.
     */
    public function drugStore(Request $request)
    {
        $typeId = $request->input('drug_type_id');

        $query = Drug::with('drugType')->orderBy('name');

        if ($typeId) {
            $query->where('drug_type_id', $typeId);
        }

        $drugs     = $query->get();
        $drugTypes = DrugType::orderBy('name')->get();

        $summary = [
            'total_items'   => $drugs->count(),
            'total_stock'   => $drugs->sum('stock_quantity'),
            'low_stock'     => $drugs->filter(fn($d) => $d->isLowStock() && $d->stock_quantity > 0)->count(),
            'out_of_stock'  => $drugs->where('stock_quantity', 0)->count(),
        ];

        return view('reports.drug-store', compact('drugs', 'drugTypes', 'summary', 'typeId'));
    }

    /**
     * Financial statement report filtered by date range.
     */
    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date');
        $endDate   = $request->date('end_date');

        $invoices = Invoice::with(['patient', 'items', 'paymentType', 'cashier'])
            ->whereBetween('invoice_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();

        // Load items for computed properties
        $invoices->each(fn($inv) => $inv->setRelation('items', $inv->items));

        $totalActual   = $invoices->sum(fn($i) => $i->actual_amount);
        $totalDiscount = $invoices->sum(fn($i) => $i->total_discount);
        $totalPayable  = $invoices->sum(fn($i) => $i->total_pay);
        $totalReceived = $invoices->sum('money_paid');

        // Group by payment type
        $byPaymentType = $invoices->groupBy(fn($i) => $i->paymentType?->name ?? 'Unknown');

        $summary = [
            'total_invoices' => $invoices->count(),
            'total_actual'   => $totalActual,
            'total_discount' => $totalDiscount,
            'total_payable'  => $totalPayable,
            'total_received' => $totalReceived,
        ];

        return view('reports.financial', compact(
            'invoices', 'summary', 'byPaymentType', 'startDate', 'endDate'
        ));
    }
}
