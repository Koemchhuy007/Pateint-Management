<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PaymentType;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * List all patients who have at least one non-discharged visit.
     */
    public function index()
    {
        $patients = Patient::whereHas('visits', fn($q) => $q->whereNull('discharge_date'))
            ->with([
                'visits' => fn($q) => $q->whereNull('discharge_date')
                                        ->latest('visit_date')
                                        ->limit(1),
            ])
            ->withCount('invoices')
            ->orderBy('given_name')
            ->get();

        return view('invoices.index', compact('patients'));
    }

    /**
     * Show the payment interface for a specific patient.
     */
    public function show(Patient $patient)
    {
        $activeVisit = $patient->visits()
            ->whereNull('discharge_date')
            ->latest('visit_date')
            ->first();

        $invoices = Invoice::with(['items', 'paymentType', 'cashier'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->get();

        $paymentTypes  = PaymentType::where('is_active', true)->orderBy('name')->get();
        $serviceGroups = ServiceGroup::with([
            'services' => fn($q) => $q->where('is_active', true)->orderBy('name'),
        ])->orderBy('name')->get();

        return view('invoices.show', compact(
            'patient', 'activeVisit', 'invoices', 'paymentTypes', 'serviceGroups'
        ));
    }

    /**
     * Store a new invoice (JSON request from the payment interface).
     */
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'payment_type_id'        => 'required|exists:payment_types,id',
            'ward'                   => 'nullable|string|max:100',
            'remark'                 => 'nullable|string|max:1000',
            'invoice_date'           => 'required|date',
            'money_paid'             => 'nullable|numeric|min:0',
            'items'                  => 'required|array|min:1',
            'items.*.service_name'   => 'required|string|max:150',
            'items.*.service_id'     => 'nullable|exists:services,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.unit_price'     => 'required|numeric|min:0',
            'items.*.discount_pct'   => 'nullable|numeric|min:0|max:100',
        ]);

        $activeVisit = $patient->visits()
            ->whereNull('discharge_date')
            ->latest('visit_date')
            ->first();

        $invoice = Invoice::create([
            'patient_id'       => $patient->id,
            'patient_visit_id' => $activeVisit?->id,
            'invoice_number'   => Invoice::generateNumber(),
            'payment_type_id'  => $validated['payment_type_id'],
            'ward'             => $validated['ward'] ?? null,
            'remark'           => $validated['remark'] ?? null,
            'cashier_id'       => auth()->id(),
            'invoice_date'     => $validated['invoice_date'],
            'money_paid'       => $validated['money_paid'] ?? 0,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'service_id'   => $item['service_id'] ?? null,
                'service_name' => $item['service_name'],
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'],
                'discount_pct' => $item['discount_pct'] ?? 0,
            ]);
        }

        $invoice->load(['items', 'paymentType', 'cashier']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice saved successfully.',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Delete an invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $patientId = $invoice->patient_id;
        $invoice->delete();

        return response()->json(['success' => true]);
    }
}
