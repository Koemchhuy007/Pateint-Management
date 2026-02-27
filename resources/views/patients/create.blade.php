@extends('layouts.app')

@section('title', __('patient.create'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">{{ __('patient.title') }}</a></li>
<li class="breadcrumb-item active">{{ __('patient.create') }}</li>
@endsection

@section('content')
<form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('patients._form', ['patient' => new \App\Models\Patient()])

    {{-- Action buttons --}}
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>{{ __('patient.save') }}
        </button>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
    </div>
</form>
@endsection
