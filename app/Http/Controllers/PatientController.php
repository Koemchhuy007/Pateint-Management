<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::query()
            ->with(['province', 'district', 'community', 'village'])
            ->search($request->search)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('patients.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'string', 'max:50', 'unique:patients,patient_id'],
            'surname' => ['required', 'string', 'max:100'],
            'given_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'sex' => ['required', Rule::in(['male', 'female', 'other'])],
            'personal_status' => ['nullable', Rule::in(['single', 'married', 'divorced'])],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'community_id' => ['nullable', 'exists:communities,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'medical_notes' => ['nullable', 'string'],
            'insurance_info' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])],
        ]);

        Patient::create($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['province', 'district', 'community', 'village']);
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
            'patient_id' => ['required', 'string', 'max:50', Rule::unique('patients', 'patient_id')->ignore($patient->id)],
            'surname' => ['required', 'string', 'max:100'],
            'given_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'sex' => ['required', Rule::in(['male', 'female', 'other'])],
            'personal_status' => ['nullable', Rule::in(['single', 'married', 'divorced'])],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'community_id' => ['nullable', 'exists:communities,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'medical_notes' => ['nullable', 'string'],
            'insurance_info' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])],
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}
