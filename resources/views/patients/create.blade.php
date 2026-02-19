@extends('layouts.app')

@section('title', 'Add Patient')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item active">Add Patient</li>
@endsection

@section('content')
<form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('patients._form', ['patient' => new \App\Models\Patient()])

    {{-- Action buttons --}}
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Save Patient
        </button>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
