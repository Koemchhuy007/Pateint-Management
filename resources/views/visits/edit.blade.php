@extends('layouts.app')

@section('title', 'Edit Visit — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">Edit Case</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-check me-2"></i>Edit Visit
        <span class="text-muted ms-1">— {{ $patient->full_name }} &middot; {{ $visit->visit_date->format('M d, Y H:i') }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('patients.visits.update', [$patient, $visit]) }}" method="POST">
            @csrf
            @method('PUT')
            @include('visits._form')
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update Visit
                </button>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
