@extends('layouts.app')

@section('title', 'Visit — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">{{ __('patient.title') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">{{ $visit->visit_date->format('d/m/Y') }}</li>
@endsection

@push('styles')
<style>
/* ── Print: hide chrome, show visit card full-width ── */
@media print {
    .topbar,
    .subnav,
    .breadcrumb-bar,
    .alert,
    .alert-dismissible,
    .col-md-3,
    .info-panel,
    .no-print { display: none !important; }

    .col-md-9 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    .row { margin: 0 !important; }
    .page-content { padding: 12px !important; }
    .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
}
</style>
@endpush

@section('content')

<div class="row g-4 align-items-start">

    {{-- ════════════════════════════════
         LEFT — Patient Info Panel
    ════════════════════════════════ --}}
    @include('patients._info_panel')

    {{-- ════════════════════════════════
         RIGHT — Visit Detail
    ════════════════════════════════ --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-clipboard-pulse me-2"></i>
                    Visit on {{ $visit->visit_date->format('F d, Y \a\t H:i') }}
                </span>
                <div class="d-flex gap-2 no-print">
                    <a href="{{ route('patients.visits.edit', [$patient, $visit]) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>{{ __('common.edit') }}
                    </a>
                    <button type="button" onclick="window.print()"
                            class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-printer me-1"></i>{{ __('visit.print') }}
                    </button>
                    <a href="{{ route('patients.show', $patient) }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- ── Visit Details ── --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th width="40%">{{ __('visit.visit_date') }}</th><td>{{ $visit->visit_date->format('d/m/Y H:i') }}</td></tr>
                            <tr>
                                <th>{{ __('field.visit_type') }}</th>
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
                            <tr>
                                <th>Follow-up</th>
                                <td>{{ $visit->follow_up_date ? $visit->follow_up_date->format('d/m/Y') : '—' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($visit->discharge_date)
                                        <span class="text-success">
                                            <i class="bi bi-check-circle me-1"></i>Discharged {{ $visit->discharge_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">{{ __('patient.active_case') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr class="my-3">

                {{-- ── Reason ── --}}
                <div class="mb-3">
                    <div class="fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.8px;color:#94a3b8;">
                        Reason / Chief Complaint
                    </div>
                    <div class="mt-1">{{ $visit->reason ?: '—' }}</div>
                </div>

                {{-- ── Diagnosis ── --}}
                <div class="mb-3">
                    <div class="fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.8px;color:#94a3b8;">
                        Diagnosis
                    </div>
                    <div class="mt-1 text-muted" style="white-space:pre-wrap;">{{ $visit->diagnosis ?: '—' }}</div>
                </div>

                {{-- ── Treatment / Prescription ── --}}
                <div class="mb-3">
                    <div class="fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.8px;color:#94a3b8;">
                        Treatment / Prescription
                    </div>
                    <div class="mt-1 text-muted" style="white-space:pre-wrap;">{{ $visit->treatment ?: '—' }}</div>
                </div>

                {{-- ── Notes ── --}}
                @if($visit->notes)
                <div class="mb-3">
                    <div class="fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.8px;color:#94a3b8;">
                        Notes
                    </div>
                    <div class="mt-1 text-muted" style="white-space:pre-wrap;">{{ $visit->notes }}</div>
                </div>
                @endif

                {{-- ── Doctor's Prescription ── --}}
                @if(!empty($visit->prescription) || $visit->consulting)
                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold" style="color:#16a34a;font-size:.95rem;">
                        <i class="bi bi-capsule-pill me-1"></i>{{ __('visit.doctors_prescription') }}
                    </div>
                    @if(!empty($visit->prescription))
                    <button type="button"
                            onclick="printPrescription()"
                            class="btn btn-sm no-print"
                            style="background:#16a34a;color:#fff;border:none;">
                        <i class="bi bi-printer me-1"></i>{{ __('visit.print_prescription') }}
                    </button>
                    @endif
                </div>

                @if(!empty($visit->prescription))
                <div class="table-responsive" id="rxPrintContent">
                    <table class="table table-bordered align-middle mb-2" style="font-size:.82rem;">
                        <thead style="background:#16a34a;color:#fff;">
                            <tr>
                                <th class="text-center" style="width:36px;">No.</th>
                                <th>Medication Name</th>
                                <th>Method</th>
                                <th class="text-center">Morning</th>
                                <th class="text-center">Afternoon</th>
                                <th class="text-center">Evening</th>
                                <th class="text-center">Night</th>
                                <th class="text-center">Number Day</th>
                                <th class="text-center">Quantity</th>
                                <th>Unit</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visit->prescription as $i => $row)
                            <tr>
                                <td class="text-center text-muted">{{ $i + 1 }}</td>
                                <td>{{ $row['medication'] ?? '—' }}</td>
                                <td>{{ $row['method']    ?? '—' }}</td>
                                <td class="text-center">{{ $row['morning']    ?: '—' }}</td>
                                <td class="text-center">{{ $row['afternoon']  ?: '—' }}</td>
                                <td class="text-center">{{ $row['evening']    ?: '—' }}</td>
                                <td class="text-center">{{ $row['night']      ?: '—' }}</td>
                                <td class="text-center">{{ $row['number_day'] ?: '—' }}</td>
                                <td class="text-center">{{ $row['quantity']   ?: '—' }}</td>
                                <td>{{ $row['unit']   ?? '—' }}</td>
                                <td>{{ $row['remark'] ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($visit->consulting)
                <div class="mt-2">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" checked disabled>
                        <label class="form-check-label fw-semibold">Consulting</label>
                    </div>
                    @if($visit->notes)
                    <div>
                        <div class="fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.8px;color:#94a3b8;">
                            Consulting Notes
                        </div>
                        <div class="mt-1 text-muted" style="white-space:pre-wrap;">{{ $visit->notes }}</div>
                    </div>
                    @endif
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Auto-print when opened with ?print=1 ──
@if(request()->has('print'))
window.addEventListener('load', function () {
    setTimeout(function () { window.print(); }, 400);
});
@endif

// ── Print Prescription popup ──
function printPrescription() {
    const rxTable = document.querySelector('#rxPrintContent table');
    if (!rxTable) return;

    const patientName = @json($patient->full_name);
    const patientId   = @json($patient->patient_id);
    const visitDate   = @json($visit->visit_date->format('d/m/Y H:i'));
    const doctor      = @json($visit->doctor_name);
    const appName     = @json(config('app.name'));

    const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Prescription — ${patientName}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body  { font-family: 'Segoe UI', Arial, sans-serif; padding: 28px 32px; font-size: 13px; color: #1e293b; }
    h2    { text-align: center; color: #1a2e4a; font-size: 20px; margin-bottom: 2px; }
    .sub  { text-align: center; color: #64748b; font-size: 12px; margin-bottom: 10px; }
    .divider { border: none; border-top: 2px solid #1a2e4a; margin: 6px 0 16px; }
    .meta { display: flex; flex-wrap: wrap; gap: 28px; margin-bottom: 18px; }
    .meta-item dt  { font-size: 10px; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
    .meta-item dd  { font-size: 13px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead th { background: #16a34a; color: #fff; padding: 7px 8px; font-weight: 600; text-align: left; }
    tbody td { border: 1px solid #d1fae5; padding: 5px 8px; }
    tbody tr:nth-child(even) td { background: #f0fdf4; }
    .tc   { text-align: center; }
    .sig  { display: flex; justify-content: flex-end; margin-top: 40px; }
    .sig-inner { text-align: center; }
    .sig-line { border-top: 1px solid #1e293b; width: 190px; margin: 44px auto 6px; }
    .sig-lbl  { font-size: 12px; color: #475569; }
    @media print { body { padding: 12px; } }
  </style>
</head>
<body>
  <h2>${appName}</h2>
  <div class="sub">Doctor's Prescription</div>
  <hr class="divider">
  <dl class="meta">
    <div class="meta-item"><dt>Patient</dt><dd>${patientName}</dd></div>
    <div class="meta-item"><dt>Patient ID</dt><dd>${patientId}</dd></div>
    <div class="meta-item"><dt>Visit Date</dt><dd>${visitDate}</dd></div>
    <div class="meta-item"><dt>Doctor</dt><dd>${doctor}</dd></div>
  </dl>
  ${rxTable.outerHTML}
  <div class="sig">
    <div class="sig-inner">
      <div class="sig-line"></div>
      <div class="sig-lbl">Doctor's Signature &amp; Stamp</div>
    </div>
  </div>
</body>
</html>`;

    const w = window.open('', '_blank', 'width=980,height=720');
    w.document.write(html);
    w.document.close();
    w.onload = function () { w.focus(); w.print(); };
}
</script>
@endpush
