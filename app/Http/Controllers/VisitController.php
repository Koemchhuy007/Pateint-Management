<?php

namespace App\Http\Controllers;

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

        $patient->visits()->create($validated);

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

        $visit->update($validated);

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
        $visit->delete();

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Visit deleted successfully.');
    }

    private function authorizeVisit(Patient $patient, PatientVisit $visit): void
    {
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }
    }
}
