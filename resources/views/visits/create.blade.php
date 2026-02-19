@extends('layouts.app')

@section('title', 'Add Visit — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">Add New Case</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-plus me-2"></i>Add Visit
        <span class="text-muted ms-1">— {{ $patient->full_name }} ({{ $patient->patient_id }})</span>
    </div>
    <div class="card-body">
        <form action="{{ route('patients.visits.store', $patient) }}" method="POST">
            @csrf
            @include('visits._form')
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Visit
                </button>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
