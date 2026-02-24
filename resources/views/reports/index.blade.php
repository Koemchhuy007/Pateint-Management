@extends('layouts.app')

@section('title', 'Reports')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1" style="color:#1e293b;">
        <i class="bi bi-bar-chart-line-fill me-2" style="color:#2563eb;"></i>Reports
    </h5>
    <div class="text-muted" style="font-size:.88rem;">Select a report type to set a date range and generate data.</div>
</div>

<div class="row g-3">

    {{-- Patient Visit Report --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 report-card" style="border-top:4px solid #2563eb;"
             onclick="openDateModal('Patient Visit Report','{{ route('reports.patient-visits') }}','bi-person-lines-fill','#2563eb','#eff6ff')">
            <div class="card-body p-4">
                <div class="report-icon mb-3" style="background:#eff6ff;">
                    <i class="bi bi-person-lines-fill" style="color:#2563eb;"></i>
                </div>
                <div class="fw-bold mb-1" style="color:#1e293b;">Patient Visit Report</div>
                <div class="text-muted" style="font-size:.82rem; line-height:1.5;">
                    View all patient visits within a date range. Filter by OPD / IPD type and discharge status.
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pb-3 px-4">
                <span style="color:#2563eb; font-size:.82rem; font-weight:600;">
                    <i class="bi bi-calendar-range me-1"></i>Select Dates <i class="bi bi-arrow-right ms-1"></i>
                </span>
            </div>
        </div>
    </div>

    {{-- Drug Usage Report --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 report-card" style="border-top:4px solid #16a34a;"
             onclick="openDateModal('Drug Usage Report','{{ route('reports.drug-usage') }}','bi-capsule-pill','#16a34a','#f0fdf4')">
            <div class="card-body p-4">
                <div class="report-icon mb-3" style="background:#f0fdf4;">
                    <i class="bi bi-capsule-pill" style="color:#16a34a;"></i>
                </div>
                <div class="fw-bold mb-1" style="color:#1e293b;">Drug Usage Report</div>
                <div class="text-muted" style="font-size:.82rem; line-height:1.5;">
                    Summarise drugs dispensed via prescriptions within a selected date range.
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pb-3 px-4">
                <span style="color:#16a34a; font-size:.82rem; font-weight:600;">
                    <i class="bi bi-calendar-range me-1"></i>Select Dates <i class="bi bi-arrow-right ms-1"></i>
                </span>
            </div>
        </div>
    </div>

    {{-- Drug Store Report (no date filter — go directly) --}}
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('reports.drug-store') }}" class="text-decoration-none">
            <div class="card h-100 report-card" style="border-top:4px solid #d97706;">
                <div class="card-body p-4">
                    <div class="report-icon mb-3" style="background:#fffbeb;">
                        <i class="bi bi-box-seam-fill" style="color:#d97706;"></i>
                    </div>
                    <div class="fw-bold mb-1" style="color:#1e293b;">Drug Store Report</div>
                    <div class="text-muted" style="font-size:.82rem; line-height:1.5;">
                        View current drug inventory levels. Highlights low stock and out-of-stock items.
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pb-3 px-4">
                    <span style="color:#d97706; font-size:.82rem; font-weight:600;">
                        View Current Stock <i class="bi bi-arrow-right ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>

    {{-- Financial Statement --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 report-card" style="border-top:4px solid #7c3aed;"
             onclick="openDateModal('Financial Statement','{{ route('reports.financial') }}','bi-cash-stack','#7c3aed','#f5f3ff')">
            <div class="card-body p-4">
                <div class="report-icon mb-3" style="background:#f5f3ff;">
                    <i class="bi bi-cash-stack" style="color:#7c3aed;"></i>
                </div>
                <div class="fw-bold mb-1" style="color:#1e293b;">Financial Statement</div>
                <div class="text-muted" style="font-size:.82rem; line-height:1.5;">
                    Review invoice totals, discounts, amounts received, and breakdowns by payment type.
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pb-3 px-4">
                <span style="color:#7c3aed; font-size:.82rem; font-weight:600;">
                    <i class="bi bi-calendar-range me-1"></i>Select Dates <i class="bi bi-arrow-right ms-1"></i>
                </span>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     DATE RANGE MODAL
═══════════════════════════════════════ --}}
<div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,.18);">

            {{-- Header --}}
            <div class="modal-header border-0 pb-2" id="modalHeader">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalIconWrap" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                        <i id="modalIconEl" class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="dateRangeModalLabel" style="color:#1e293b; font-size:1rem;">Generate Report</h6>
                        <div class="text-muted" style="font-size:.78rem;">Select a date range to pull the report</div>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Form --}}
            <form method="GET" id="dateRangeForm" action="">
                <div class="modal-body pt-2 pb-3 px-4">

                    <div class="row g-3 mt-1">

                        {{-- Start Date --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:.83rem;">
                                Start Date <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text"
                                       id="modal_start_date"
                                       name="start_date"
                                       class="form-control"
                                       placeholder="Select…"
                                       autocomplete="off"
                                       readonly
                                       required>
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="document.getElementById('modal_start_date')._flatpickr.open()"
                                        tabindex="-1">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                            </div>
                        </div>

                        {{-- End Date --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:.83rem;">
                                End Date <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text"
                                       id="modal_end_date"
                                       name="end_date"
                                       class="form-control"
                                       placeholder="Select…"
                                       autocomplete="off"
                                       readonly
                                       required>
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="document.getElementById('modal_end_date')._flatpickr.open()"
                                        tabindex="-1">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- Inline validation message --}}
                    <div id="modalDateError" class="mt-2 d-none" style="font-size:.8rem; color:#dc2626;">
                        <i class="bi bi-exclamation-circle me-1"></i>Please select both start and end dates.
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-sm px-4" id="modalGenerateBtn"
                            onclick="submitDateModal()" style="color:#fff; min-width:130px;">
                        <i class="bi bi-search me-1"></i>Generate Report
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.report-card {
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
}
.report-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
}
.report-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
}

/* Flatpickr inside modal */
.flatpickr-input[readonly] { background-color: #fff; cursor: pointer; }
.input-group .flatpickr-input.form-control[style*="display: none"] + .flatpickr-input {
    flex: 1 1 auto;
    min-width: 0;
    border-radius: 0.25rem 0 0 0.25rem !important;
}
/* Bump modal z-index above Bootstrap's default so calendar shows on top */
.flatpickr-calendar { z-index: 9999 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let modalStartPicker, modalEndPicker;
let _activeAccentColor = '#2563eb';

document.addEventListener('DOMContentLoaded', function () {

    modalStartPicker = flatpickr('#modal_start_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j M Y',
        maxDate: 'today',
        allowInput: false,
        onChange: function (selectedDates, dateStr) {
            if (dateStr) modalEndPicker.set('minDate', dateStr);
        }
    });

    modalEndPicker = flatpickr('#modal_end_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j M Y',
        maxDate: 'today',
        allowInput: false,
        onChange: function (selectedDates, dateStr) {
            if (dateStr) modalStartPicker.set('maxDate', dateStr);
        }
    });

    // Reset pickers and error when modal is hidden
    document.getElementById('dateRangeModal').addEventListener('hidden.bs.modal', function () {
        modalStartPicker.clear();
        modalEndPicker.clear();
        modalStartPicker.set('maxDate', 'today');
        modalEndPicker.set('minDate', null);
        document.getElementById('modalDateError').classList.add('d-none');
    });
});

/**
 * Open the date range modal, styled for the chosen report.
 */
function openDateModal(title, url, iconClass, color, bgColor) {
    _activeAccentColor = color;

    // Icon
    var iconEl = document.getElementById('modalIconEl');
    iconEl.className = 'bi ' + iconClass;
    iconEl.style.color = color;
    document.getElementById('modalIconWrap').style.background = bgColor;

    // Title
    document.getElementById('dateRangeModalLabel').textContent = title;

    // Form action
    document.getElementById('dateRangeForm').action = url;

    // Generate button accent
    var btn = document.getElementById('modalGenerateBtn');
    btn.style.background = color;
    btn.style.borderColor = color;

    new bootstrap.Modal(document.getElementById('dateRangeModal')).show();
}

/**
 * Validate and submit the modal form.
 */
function submitDateModal() {
    var startVal = document.getElementById('modal_start_date').value;
    var endVal   = document.getElementById('modal_end_date').value;
    var errEl    = document.getElementById('modalDateError');

    if (!startVal || !endVal) {
        errEl.classList.remove('d-none');
        return;
    }

    errEl.classList.add('d-none');
    document.getElementById('dateRangeForm').submit();
}
</script>
@endpush
@endsection
