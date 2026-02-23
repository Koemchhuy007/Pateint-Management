<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\Patient;
use App\Models\PatientVisit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VisitController extends Controller
{
    public function create(Patient $patient)
    {
        return view('visits.create', compact('patient'));
    }

    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'visit_date'     => ['required', 'date'],
            'visit_type'     => ['required', Rule::in(['OPD', 'IPD'])],
            'reason'         => ['required', 'string', 'max:500'],
            'diagnosis'      => ['nullable', 'string'],
            'treatment'      => ['nullable', 'string'],
            'doctor_name'    => ['required', 'string', 'max:100'],
            'follow_up_date' => ['nullable', 'date', 'after:visit_date'],
            'notes'          => ['nullable', 'string'],
        ]);

        $validated['consulting']   = $request->boolean('consulting');
        $validated['prescription'] = $this->cleanPrescription($request->input('prescription', []));

        $patient->visits()->create($validated);

        // Deduct drug stock for all dispensed items
        $this->deductPrescriptionStock($validated['prescription']);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Visit recorded successfully.');
    }

    public function show(Patient $patient, PatientVisit $visit)
    {
        $this->authorizeVisit($patient, $visit);
        return view('visits.show', compact('patient', 'visit'));
    }

    public function edit(Patient $patient, PatientVisit $visit)
    {
        $this->authorizeVisit($patient, $visit);
        return view('visits.edit', compact('patient', 'visit'));
    }

    public function update(Request $request, Patient $patient, PatientVisit $visit)
    {
        $this->authorizeVisit($patient, $visit);

        $validated = $request->validate([
            'visit_date'     => ['required', 'date'],
            'visit_type'     => ['required', Rule::in(['OPD', 'IPD'])],
            'reason'         => ['required', 'string', 'max:500'],
            'diagnosis'      => ['nullable', 'string'],
            'treatment'      => ['nullable', 'string'],
            'doctor_name'    => ['required', 'string', 'max:100'],
            'follow_up_date' => ['nullable', 'date', 'after:visit_date'],
            'notes'          => ['nullable', 'string'],
        ]);

        $validated['consulting']   = $request->boolean('consulting');
        $validated['prescription'] = $this->cleanPrescription($request->input('prescription', []));

        // Restore stock from the OLD prescription before overwriting
        $this->restorePrescriptionStock($visit->prescription ?? []);

        $visit->update($validated);

        // Deduct stock for the NEW prescription
        $this->deductPrescriptionStock($validated['prescription']);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Visit updated successfully.');
    }

    public function discharge(Patient $patient, PatientVisit $visit)
    {
        $this->authorizeVisit($patient, $visit);

        $visit->update(['discharge_date' => now()->toDateString()]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Visit discharged on ' . now()->format('d/m/Y') . '.');
    }

    public function destroy(Patient $patient, PatientVisit $visit)
    {
        $this->authorizeVisit($patient, $visit);

        // Restore stock when a visit with a prescription is deleted
        $this->restorePrescriptionStock($visit->prescription ?? []);

        $visit->delete();

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Visit deleted successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Remove empty rows and normalise drug_id to int.
     */
    private function cleanPrescription(array $rows): array
    {
        return array_values(array_filter(
            array_map(function ($r) {
                $r['drug_id'] = (int) ($r['drug_id'] ?? 0);
                return $r;
            }, $rows),
            fn ($r) => !empty($r['medication'])
        ));
    }

    /**
     * Deduct the dispensed quantity from each linked drug's stock.
     */
    private function deductPrescriptionStock(array $rows): void
    {
        foreach ($rows as $row) {
            $drugId = (int) ($row['drug_id'] ?? 0);
            $qty    = (int) ($row['quantity'] ?? 0);
            if ($drugId > 0 && $qty > 0) {
                Drug::whereKey($drugId)->decrement('stock_quantity', $qty);
            }
        }
    }

    /**
     * Add back previously deducted quantities (used before update/delete).
     */
    private function restorePrescriptionStock(array $rows): void
    {
        foreach ($rows as $row) {
            $drugId = (int) ($row['drug_id'] ?? 0);
            $qty    = (int) ($row['quantity'] ?? 0);
            if ($drugId > 0 && $qty > 0) {
                Drug::whereKey($drugId)->increment('stock_quantity', $qty);
            }
        }
    }

    private function authorizeVisit(Patient $patient, PatientVisit $visit): void
    {
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }
    }
}
