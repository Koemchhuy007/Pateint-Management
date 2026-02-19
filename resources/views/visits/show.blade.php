@extends('layouts.app')

@section('title', 'Visit — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">{{ $visit->visit_date->format('d/m/Y') }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">
        <i class="bi bi-clipboard-pulse me-2"></i>Visit on {{ $visit->visit_date->format('F d, Y \a\t H:i') }}
    </h5>
    <div class="btn-group">
        <a href="{{ route('patients.visits.edit', [$patient, $visit]) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <form action="{{ route('patients.visits.destroy', [$patient, $visit]) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('Delete this visit record?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Visit Details</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th width="40%">Visit Date</th><td>{{ $visit->visit_date->format('F d, Y H:i') }}</td></tr>
                    <tr>
                        <th>Visit Type</th>
                        <td>
                            @if($visit->visit_type === 'OPD')
                                <span class="badge" style="background:#0ea5e9;">OPD</span>
                            @elseif($visit->visit_type === 'IPD')
                                <span class="badge" style="background:#8b5cf6;">IPD</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Doctor</th><td>{{ $visit->doctor_name }}</td></tr>
                    <tr><th>Reason</th><td>{{ $visit->reason }}</td></tr>
                    <tr>
                        <th>Follow-up</th>
                        <td>{{ $visit->follow_up_date ? $visit->follow_up_date->format('F d, Y') : '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Clinical</div>
            <div class="card-body">
                <p class="mb-1 fw-semibold">Diagnosis</p>
                <p class="text-muted">{{ $visit->diagnosis ?: '—' }}</p>
                <p class="mb-1 fw-semibold">Treatment / Prescription</p>
                <p class="text-muted mb-0">{{ $visit->treatment ?: '—' }}</p>
            </div>
        </div>
    </div>
</div>

@if($visit->notes)
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header">Notes</div>
            <div class="card-body">
                <p class="mb-0 text-muted">{{ $visit->notes }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
