@extends('layouts.app')

@section('title', 'Drug Usage Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Drug Usage Report</li>
@endsection

@section('content')
<div class="mb-3 d-flex align-items-center gap-2">
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <h5 class="fw-bold mb-0" style="color:#1e293b;">
        <i class="bi bi-capsule-pill me-2" style="color:#16a34a;"></i>Drug Usage Report
    </h5>
</div>

{{-- Filter Form --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.drug-usage') }}" class="row g-3 align-items-end">

            {{-- Start Date --}}
            <div class="col-sm-4 col-md-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;">
                    Start Date <span class="text-danger">*</span>
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
                    End Date <span class="text-danger">*</span>
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
                <button type="submit" class="btn btn-primary" style="background:#16a34a;border-color:#16a34a;">
                    <i class="bi bi-search me-1"></i>Generate Report
                </button>
            </div>
            @isset($startDate)
            <div class="col-auto">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
            @endisset
        </form>
    </div>
</div>

@isset($usage)
{{-- Summary --}}
<div class="mb-2 text-muted" style="font-size:.82rem;">
    Found <strong>{{ count($usage) }}</strong> distinct drug(s) dispensed across
    <strong>{{ $visitCount }}</strong> visit(s) from
    <strong>{{ $startDate->format('d/m/Y') }}</strong> to <strong>{{ $endDate->format('d/m/Y') }}</strong>
</div>

<div class="card">
    <div class="card-body p-0">
        @if(empty($usage))
            <div class="text-center text-muted py-5">
                <i class="bi bi-capsule" style="font-size:2.5rem; opacity:.35;"></i>
                <div class="mt-2">No drug usage found for the selected date range.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.84rem;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Medication Name</th>
                        <th>Unit</th>
                        <th class="text-end">Times Dispensed</th>
                        <th class="text-end pe-3">Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usage as $i => $row)
                    <tr>
                        <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold">
                            {{ $row['medication'] }}
                            @if(!$row['drug_id'])
                                <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Manual</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $row['unit'] ?: '—' }}</td>
                        <td class="text-end">{{ $row['visit_count'] }}</td>
                        <td class="text-end pe-3">
                            <span class="fw-semibold">{{ number_format($row['total_qty']) }}</span>
                            @if($row['unit'])
                                <span class="text-muted"> {{ $row['unit'] }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background:#f8fafc;">
                    <tr>
                        <th colspan="3" class="ps-3">Total</th>
                        <th class="text-end">{{ array_sum(array_column($usage, 'visit_count')) }}</th>
                        <th class="text-end pe-3">{{ number_format(array_sum(array_column($usage, 'total_qty'))) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endisset

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.flatpickr-input[readonly] { background-color: #fff; cursor: pointer; }
.input-group .flatpickr-input.form-control[style*="display: none"] + .flatpickr-input {
    flex: 1 1 auto;
    min-width: 0;
    border-radius: 0.375rem 0 0 0.375rem !important;
}
.is-invalid + .flatpickr-input { border-color: #dc3545 !important; }
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
