<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::query()
            ->with(['province', 'district', 'community', 'village'])
            ->withMax('visits', 'visit_date')
            ->search($request->search)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        $patientId = $this->generatePatientId();
        return view('patients.create', compact('provinces', 'patientId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'surname'                 => ['required', 'string', 'max:100'],
            'given_name'              => ['required', 'string', 'max:100'],
            'date_of_birth'           => ['required', 'date', 'before:today'],
            'sex'                     => ['required', Rule::in(['male', 'female', 'other'])],
            'personal_status'         => ['nullable', Rule::in(['single', 'married', 'divorced'])],
            'email'                   => ['nullable', 'email', 'max:255'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'address'                 => ['nullable', 'string'],
            'province_id'             => ['nullable', 'exists:provinces,id'],
            'district_id'             => ['nullable', 'exists:districts,id'],
            'community_id'            => ['nullable', 'exists:communities,id'],
            'village_id'              => ['nullable', 'exists:villages,id'],
            'blood_type'              => ['nullable', 'string', 'max:10'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'medical_notes'           => ['nullable', 'string'],
            'insurance_info'          => ['nullable', 'string', 'max:255'],
            'status'                  => ['required', Rule::in(['active', 'inactive', 'archived'])],
            'photo'                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Use the ID previewed on the form if it is still free; otherwise generate a fresh one
        $submitted = $request->input('patient_id');
        $validated['patient_id'] = ($submitted && !Patient::where('patient_id', $submitted)->exists())
            ? $submitted
            : $this->generatePatientId();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('patients/photos', 'public');
        }

        Patient::create($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['province', 'district', 'community', 'village', 'visits']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $provinces = Province::orderBy('name')->get();
        $patient->load(['province', 'district', 'community', 'village']);
        return view('patients.edit', compact('patient', 'provinces'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'surname'                 => ['required', 'string', 'max:100'],
            'given_name'              => ['required', 'string', 'max:100'],
            'date_of_birth'           => ['required', 'date', 'before:today'],
            'sex'                     => ['required', Rule::in(['male', 'female', 'other'])],
            'personal_status'         => ['nullable', Rule::in(['single', 'married', 'divorced'])],
            'email'                   => ['nullable', 'email', 'max:255'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'address'                 => ['nullable', 'string'],
            'province_id'             => ['nullable', 'exists:provinces,id'],
            'district_id'             => ['nullable', 'exists:districts,id'],
            'community_id'            => ['nullable', 'exists:communities,id'],
            'village_id'              => ['nullable', 'exists:villages,id'],
            'blood_type'              => ['nullable', 'string', 'max:10'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'medical_notes'           => ['nullable', 'string'],
            'insurance_info'          => ['nullable', 'string', 'max:255'],
            'status'                  => ['required', Rule::in(['active', 'inactive', 'archived'])],
            'photo'                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if one exists
            if ($patient->photo) {
                Storage::disk('public')->delete($patient->photo);
            }
            $validated['photo'] = $request->file('photo')->store('patients/photos', 'public');
        }

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        if ($patient->photo) {
            Storage::disk('public')->delete($patient->photo);
        }
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    public function openCase(Patient $patient)
    {
        $patient->update(['active_case' => true]);
        $patient->load('visits');
        return view('patients.case', compact('patient'));
    }

    public function showCase(Patient $patient)
    {
        $patient->load('visits');
        return view('patients.case', compact('patient'));
    }

    public function discardCase(Patient $patient)
    {
        $patient->update(['active_case' => false]);

        return redirect()->route('patients.index')
            ->with('success', 'Case discarded for ' . $patient->full_name . '.');
    }

    private function generatePatientId(): string
    {
        // Find the highest numeric suffix across ALL existing patient IDs
        $max = 0;
        Patient::select('patient_id')->each(function ($p) use (&$max) {
            if (preg_match('/(\d+)$/', $p->patient_id, $m)) {
                $max = max($max, (int) $m[1]);
            }
        });

        // Increment and keep looping until we land on a slot that isn't taken
        do {
            $max++;
            $candidate = 'PAT-' . str_pad($max, 5, '0', STR_PAD_LEFT);
        } while (Patient::where('patient_id', $candidate)->exists());

        return $candidate;
    }
}
