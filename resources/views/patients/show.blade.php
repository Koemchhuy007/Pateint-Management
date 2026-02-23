@extends('layouts.app')

@section('title', $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item active">{{ $patient->full_name }}</li>
@endsection

@push('styles')
<style>
/* ── Active (undischarged) visit row ── */
.visit-active-row {
    background: linear-gradient(90deg, rgba(16, 185, 129, .08) 0%, rgba(16, 185, 129, .03) 100%);
    border-left: 4px solid #10b981;
}

.visit-active-row td:first-child {
    padding-left: 10px;
}

.active-case-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .68rem;
    font-weight: 700;
    color: #059669;
    background: #d1fae5;
    border-radius: 20px;
    padding: 2px 8px;
    margin-top: 3px;
}
</style>
@endpush

@section('content')

{{-- ── Two-column layout ── --}}
<div class="row g-4 align-items-start">

    {{-- ════════════════════════════════
         LEFT — Patient Info Panel
    ════════════════════════════════ --}}
    @include('patients._info_panel')

    {{-- ════════════════════════════════
         RIGHT — Visit History
    ════════════════════════════════ --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-clipboard-pulse me-2"></i>Visit History
                    <span class="badge bg-secondary ms-1">{{ $patient->visits->count() }}</span>
                </span>
                <a href="{{ route('patients.visits.create', $patient) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Add New Case
                </a>
            </div>
            <div class="card-body p-0">
                @if($patient->visits->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <p class="mb-2">No visits recorded for this patient yet.</p>
                    <a href="{{ route('patients.visits.create', $patient) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Add New Case
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Discharge Date</th>
                                <th class="text-center">Type</th>
                                <th>Doctor</th>
                                <th>Reason / Diagnosis / Treatment</th>
                                <th>Follow-up</th>
                                <th style="width:130px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patient->visits as $i => $visit)
                            @php $isActive = !$visit->discharge_date; @endphp
                            <tr class="{{ $isActive ? 'visit-active-row' : '' }}">
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $visit->visit_date->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size:.78rem;">{{ $visit->visit_date->format('H:i') }}</div>
                                    @if($isActive)
                                    <span class="active-case-badge">
                                        <i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Active
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($visit->discharge_date)
                                    <span class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>{{ $visit->discharge_date->format('d/m/Y') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($visit->visit_type === 'OPD')
                                    <span class="badge" style="background:#0ea5e9;">OPD</span>
                                    @elseif($visit->visit_type === 'IPD')
                                    <span class="badge" style="background:#8b5cf6;">IPD</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $visit->doctor_name }}</td>

                                {{-- Reason / Diagnosis / Treatment --}}
                                <td style="max-width:260px;">
                                    @if($visit->reason)
                                    <div style="font-size:.8rem;">
                                        <span class="text-muted fw-semibold">Reason:</span>
                                        {{ Str::limit($visit->reason, 60) }}
                                    </div>
                                    @endif
                                    @if($visit->diagnosis)
                                    <div style="font-size:.8rem;" class="mt-1">
                                        <span class="text-muted fw-semibold">Diagnosis:</span>
                                        {{ Str::limit($visit->diagnosis, 60) }}
                                    </div>
                                    @endif
                                    @if($visit->treatment)
                                    <div style="font-size:.8rem;" class="mt-1">
                                        <span class="text-muted fw-semibold">Treatment:</span>
                                        {{ Str::limit($visit->treatment, 80) }}
                                    </div>
                                    @endif
                                    @if(!$visit->reason && !$visit->diagnosis && !$visit->treatment)
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($visit->follow_up_date)
                                    <span class="{{ $visit->follow_up_date->isPast() ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $visit->follow_up_date->format('d/m/Y') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('patients.visits.show', [$patient, $visit]) }}"
                                            class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.visits.edit', [$patient, $visit]) }}"
                                            class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(!$visit->discharge_date)
                                        <form action="{{ route('patients.visits.discharge', [$patient, $visit]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Mark this visit as discharged today?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning" title="Discharge">
                                                <i class="bi bi-box-arrow-right"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('patients.visits.destroy', [$patient, $visit]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this visit record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection