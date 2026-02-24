@extends('layouts.app')

@section('title', 'Financial Statement')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Financial Statement</li>
@endsection

@section('content')
<div class="mb-3 d-flex align-items-center gap-2">
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <h5 class="fw-bold mb-0" style="color:#1e293b;">
        <i class="bi bi-cash-stack me-2" style="color:#7c3aed;"></i>Financial Statement
    </h5>
</div>

{{-- Filter Form --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.financial') }}" class="row g-3 align-items-end">

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
                <button type="submit" class="btn btn-primary" style="background:#7c3aed;border-color:#7c3aed;">
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

@isset($invoices)
{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.5rem;font-weight:700;color:#1e293b;">{{ $summary['total_invoices'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">Total Invoices</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.3rem;font-weight:700;color:#2563eb;">{{ number_format($summary['total_actual'], 2) }}</div>
                <div class="text-muted" style="font-size:.78rem;">Gross Amount</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.3rem;font-weight:700;color:#ef4444;">{{ number_format($summary['total_discount'], 2) }}</div>
                <div class="text-muted" style="font-size:.78rem;">Total Discount</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.3rem;font-weight:700;color:#7c3aed;">{{ number_format($summary['total_payable'], 2) }}</div>
                <div class="text-muted" style="font-size:.78rem;">Net Payable</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2-4">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.3rem;font-weight:700;color:#10b981;">{{ number_format($summary['total_received'], 2) }}</div>
                <div class="text-muted" style="font-size:.78rem;">Amount Received</div>
            </div>
        </div>
    </div>
</div>

{{-- By Payment Type Summary --}}
@if($byPaymentType->isNotEmpty())
<div class="card mb-4">
    <div class="card-header py-2" style="font-size:.88rem;">
        <i class="bi bi-pie-chart me-1"></i>Breakdown by Payment Type
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:.84rem;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-3">Payment Type</th>
                        <th class="text-end">Invoices</th>
                        <th class="text-end">Gross Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Net Payable</th>
                        <th class="text-end pe-3">Amount Received</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byPaymentType as $typeName => $group)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $typeName }}</td>
                        <td class="text-end">{{ $group->count() }}</td>
                        <td class="text-end">{{ number_format($group->sum(fn($i) => $i->actual_amount), 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($group->sum(fn($i) => $i->total_discount), 2) }}</td>
                        <td class="text-end fw-semibold">{{ number_format($group->sum(fn($i) => $i->total_pay), 2) }}</td>
                        <td class="text-end pe-3" style="color:#10b981;font-weight:600;">{{ number_format($group->sum('money_paid'), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Period label --}}
<div class="mb-2 text-muted" style="font-size:.82rem;">
    Showing <strong>{{ $summary['total_invoices'] }}</strong> invoice(s) from
    <strong>{{ $startDate->format('d/m/Y') }}</strong> to <strong>{{ $endDate->format('d/m/Y') }}</strong>
</div>

{{-- Invoice Detail Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($invoices->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-receipt" style="font-size:2.5rem; opacity:.35;"></i>
                <div class="mt-2">No invoices found for the selected date range.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.84rem;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Payment Type</th>
                        <th>Cashier</th>
                        <th class="text-end">Gross</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Net Payable</th>
                        <th class="text-end pe-3">Received</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $i => $invoice)
                    <tr>
                        <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold" style="font-size:.8rem; font-family:monospace;">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('patients.show', $invoice->patient) }}" class="text-decoration-none">
                                {{ $invoice->patient->full_name }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $invoice->paymentType?->name ?? '—' }}</td>
                        <td class="text-muted">{{ $invoice->cashier?->name ?? '—' }}</td>
                        <td class="text-end">{{ number_format($invoice->actual_amount, 2) }}</td>
                        <td class="text-end text-danger">
                            {{ $invoice->total_discount > 0 ? number_format($invoice->total_discount, 2) : '—' }}
                        </td>
                        <td class="text-end fw-semibold">{{ number_format($invoice->total_pay, 2) }}</td>
                        <td class="text-end pe-3" style="color:#10b981;font-weight:600;">
                            {{ number_format($invoice->money_paid, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background:#f8fafc;">
                    <tr>
                        <th colspan="6" class="ps-3">Total</th>
                        <th class="text-end">{{ number_format($summary['total_actual'], 2) }}</th>
                        <th class="text-end text-danger">{{ number_format($summary['total_discount'], 2) }}</th>
                        <th class="text-end">{{ number_format($summary['total_payable'], 2) }}</th>
                        <th class="text-end pe-3" style="color:#10b981;">{{ number_format($summary['total_received'], 2) }}</th>
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
