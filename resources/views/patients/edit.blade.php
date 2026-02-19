@extends('layouts.app')

@section('title', 'Edit Patient')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<form action="{{ route('patients.update', $patient) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('patients._form')

    {{-- Action buttons --}}
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Update Patient
        </button>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
