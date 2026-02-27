@extends('layouts.app')

@section('title', $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">{{ __('patient.title') }}</a></li>
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
                    <i class="bi bi-clipboard-pulse me-2"></i>{{ __('patient.visit_history') }}
                    <span class="badge bg-secondary ms-1">{{ $patient->visits->count() }}</span>
                </span>
                <a href="{{ route('patients.visits.create', $patient) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('visit.create') }}
                </a>
            </div>
            <div class="card-body p-0">
                @if($patient->visits->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <p class="mb-2">{{ __('visit.no_visits_yet') }}</p>
                    <a href="{{ route('patients.visits.create', $patient) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('visit.create') }}
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('visit.visit_date') }}</th>
                                <th>{{ __('visit.discharge_date') }}</th>
                                <th class="text-center">{{ __('field.visit_type') }}</th>
                                <th>Doctor</th>
                                <th>{{ __('visit.diagnosis') }} / {{ __('visit.prescription') }}</th>
                                <th>Follow-up</th>
                                <th style="width:44px" class="text-center">{{ __('common.actions') }}</th>
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
                                        <i class="bi bi-circle-fill" style="font-size:.45rem;"></i> {{ __('common.active') }}
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

                                {{-- ── Action Dropdown ── --}}
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary border-0 rounded-2"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                title="Actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                                            style="min-width:165px;font-size:.85rem;border-radius:10px;">

                                            <li>
                                                <a class="dropdown-item py-2"
                                                   href="{{ route('patients.visits.show', [$patient, $visit]) }}">
                                                    <i class="bi bi-eye me-2" style="color:#64748b;"></i>{{ __('common.view') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2"
                                                   href="{{ route('patients.visits.show', [$patient, $visit]) }}?print=1"
                                                   target="_blank">
                                                    <i class="bi bi-printer me-2" style="color:#0369a1;"></i>{{ __('visit.print') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2"
                                                   href="{{ route('patients.visits.edit', [$patient, $visit]) }}">
                                                    <i class="bi bi-pencil me-2" style="color:#2563eb;"></i>{{ __('common.edit') }}
                                                </a>
                                            </li>

                                            @if($isActive)
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('patients.visits.discharge', [$patient, $visit]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('{{ __('visit.discharge_confirm') }}');">
                                                    @csrf
                                                    <button type="submit"
                                                            class="dropdown-item py-2 fw-semibold"
                                                            style="color:#d97706;">
                                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('patient.discharge') }}
                                                    </button>
                                                </form>
                                            </li>
                                            @endif

                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('patients.visits.destroy', [$patient, $visit]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('{{ __('visit.delete_confirm') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                                        <i class="bi bi-trash me-2"></i>{{ __('common.delete') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
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