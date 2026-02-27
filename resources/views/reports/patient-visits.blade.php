@extends('layouts.app')

@section('title', __('report.patient_visit_report'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">{{ __('report.title') }}</a></li>
    <li class="breadcrumb-item active">{{ __('report.patient_visit_report') }}</li>
@endsection

@section('content')
<div class="mb-3 d-flex align-items-center gap-2">
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
    </a>
    <h5 class="fw-bold mb-0" style="color:#1e293b;">
        <i class="bi bi-person-lines-fill me-2" style="color:#2563eb;"></i>{{ __('report.patient_visit_report') }}
    </h5>
</div>

{{-- Filter Form --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.patient-visits') }}" class="row g-3 align-items-end">

            {{-- Start Date --}}
            <div class="col-sm-4 col-md-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;">
                    {{ __('field.start_date') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="text"
                           id="start_date"
                           name="start_date"
                           class="form-control @error('start_date') is-invalid @enderror"
                           placeholder="Select start date…"
                           value="{{ isset($startDate) ? $startDate->toDateString() : '' }}"
                           autocomplete="off"
                           required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="document.getElementById('start_date')._flatpickr.open()"
                            tabindex="-1">
                        <i class="bi bi-calendar3"></i>
                    </button>
                </div>
                @error('start_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- End Date --}}
            <div class="col-sm-4 col-md-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;">
                    {{ __('field.end_date') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="text"
                           id="end_date"
                           name="end_date"
                           class="form-control @error('end_date') is-invalid @enderror"
                           placeholder="Select end date…"
                           value="{{ isset($endDate) ? $endDate->toDateString() : '' }}"
                           autocomplete="off"
                           required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="document.getElementById('end_date')._flatpickr.open()"
                            tabindex="-1">
                        <i class="bi bi-calendar3"></i>
                    </button>
                </div>
                @error('end_date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>{{ __('report.generate') }}
                </button>
            </div>
            @isset($startDate)
            <div class="col-auto">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>{{ __('common.print') }}
                </button>
            </div>
            @endisset
        </form>
    </div>
</div>

@isset($visits)
{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#2563eb;">{{ $summary['total'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">{{ __('report.total_visits') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#0ea5e9;">{{ $summary['opd'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">OPD</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#8b5cf6;">{{ $summary['ipd'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">IPD</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#10b981;">{{ $summary['discharged'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">{{ __('report.discharged') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#f59e0b;">{{ $summary['active'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">{{ __('common.active') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Period label --}}
<div class="mb-2 text-muted" style="font-size:.82rem;">
    Showing <strong>{{ $summary['total'] }}</strong> visit(s) from
    <strong>{{ $startDate->format('d/m/Y') }}</strong> to <strong>{{ $endDate->format('d/m/Y') }}</strong>
</div>

{{-- Results Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($visits->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-calendar-x" style="font-size:2.5rem; opacity:.35;"></i>
                <div class="mt-2">{{ __('report.no_data_range') }}</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.84rem;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>{{ __('patient.title') }}</th>
                        <th>{{ __('field.patient_id') }}</th>
                        <th>{{ __('visit.visit_date') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>Doctor</th>
                        <th>Reason</th>
                        <th>{{ __('visit.diagnosis') }}</th>
                        <th>{{ __('field.status') }}</th>
                        <th>{{ __('visit.discharge_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visits as $i => $visit)
                    <tr>
                        <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold">
                            <a href="{{ route('patients.show', $visit->patient) }}" class="text-decoration-none">
                                {{ $visit->patient->full_name }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $visit->patient->patient_id }}</td>
                        <td>{{ $visit->visit_date->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($visit->visit_type === 'OPD')
                                <span class="badge" style="background:#0ea5e9;">OPD</span>
                            @else
                                <span class="badge" style="background:#8b5cf6;">IPD</span>
                            @endif
                        </td>
                        <td>{{ $visit->doctor_name }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($visit->reason, 50) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($visit->diagnosis ?? '—', 50) }}</td>
                        <td>
                            @if($visit->discharge_date)
                                <span class="badge" style="background:#10b981;">{{ __('report.discharged') }}</span>
                            @else
                                <span class="badge" style="background:#f59e0b;">{{ __('common.active') }}</span>
                            @endif
                        </td>
                        <td>{{ $visit->discharge_date ? $visit->discharge_date->format('d/m/Y') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endisset

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* Keep altInput styled like a normal form-control */
.flatpickr-input[readonly] { background-color: #fff; cursor: pointer; }
/* altInput sits between original (hidden) and the icon button in input-group */
.input-group .flatpickr-input.form-control[style*="display: none"] + .flatpickr-input {
    flex: 1 1 auto;
    min-width: 0;
    border-radius: 0.375rem 0 0 0.375rem !important;
}
/* Propagate is-invalid border to the visible altInput */
.is-invalid + .flatpickr-input { border-color: #dc3545 !important; }

.col-md-2-4 { flex: 0 0 auto; width: 20%; }
@media (max-width:767px) { .col-md-2-4 { width: 50%; } }
@media print {
    .subnav, .topbar, .breadcrumb-bar, form, .btn, a.btn { display: none !important; }
    .page-content { padding: 0 !important; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startPicker = flatpickr('#start_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j F Y',
        maxDate: 'today',
        allowInput: false,
        onChange: function (selectedDates, dateStr) {
            if (dateStr) endPicker.set('minDate', dateStr);
        }
    });

    const endPicker = flatpickr('#end_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j F Y',
        maxDate: 'today',
        allowInput: false,
        onChange: function (selectedDates, dateStr) {
            if (dateStr) startPicker.set('maxDate', dateStr);
        }
    });

    // Set initial minDate/maxDate constraints if values are already present
    @isset($startDate)
    endPicker.set('minDate', '{{ $startDate->toDateString() }}');
    @endisset
    @isset($endDate)
    startPicker.set('maxDate', '{{ $endDate->toDateString() }}');
    @endisset
});
</script>
@endpush
@endsection
