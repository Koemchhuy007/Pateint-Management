@extends('layouts.app')

@section('title', $patient->full_name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-person-vcard me-2"></i>{{ $patient->full_name }}</h4>
    <div class="btn-group">
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this patient?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Personal Information</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th width="40%">Patient ID</th><td>{{ $patient->patient_id }}</td></tr>
                    <tr><th>Surname</th><td>{{ $patient->surname }}</td></tr>
                    <tr><th>Given Name</th><td>{{ $patient->given_name }}</td></tr>
                    <tr><th>Date of Birth</th><td>{{ $patient->date_of_birth->format('F d, Y') }}</td></tr>
                    <tr><th>Age</th><td>{{ $patient->age ? $patient->age . ' years' : '—' }}</td></tr>
                    <tr><th>Sex</th><td>{{ ucfirst($patient->sex ?? '—') }}</td></tr>
                    <tr><th>Personal Status</th><td>{{ ucfirst($patient->personal_status ?? '—') }}</td></tr>
                    <tr><th>Blood Type</th><td>{{ $patient->blood_type ?? '—' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $patient->status }}">{{ ucfirst($patient->status) }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Contact Information</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th width="40%">Email</th><td>{{ $patient->email ?? '—' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $patient->phone ?? '—' }}</td></tr>
                    <tr><th>Address</th><td>{{ $patient->full_address ?: ($patient->address ?? '—') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header">Emergency Contact</div>
            <div class="card-body">
                <p class="mb-0">{{ $patient->emergency_contact_name ?? '—' }} — {{ $patient->emergency_contact_phone ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>

@if($patient->medical_notes || $patient->insurance_info)
<div class="row">
    @if($patient->medical_notes)
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Medical Notes</div>
            <div class="card-body">
                <p class="mb-0 text-muted">{{ $patient->medical_notes }}</p>
            </div>
        </div>
    </div>
    @endif
    @if($patient->insurance_info)
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Insurance</div>
            <div class="card-body">
                <p class="mb-0">{{ $patient->insurance_info }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<a href="{{ route('patients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to list</a>
@endsection
