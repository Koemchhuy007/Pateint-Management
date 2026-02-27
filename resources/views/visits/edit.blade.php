@extends('layouts.app')

@section('title', 'Edit Visit — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">{{ __('patient.title') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">Edit Case</li>
@endsection

@section('content')

<div class="row g-4 align-items-start">

    {{-- ════════════════════════════════
         LEFT — Patient Info Panel
    ════════════════════════════════ --}}
    @include('patients._info_panel')

    {{-- ════════════════════════════════
         RIGHT — Edit Visit Form
    ════════════════════════════════ --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-clipboard-check me-2"></i>{{ __('visit.edit') }}
                    <span class="text-muted ms-1">— {{ $visit->visit_date->format('M d, Y H:i') }}</span>
                </span>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.visits.update', [$patient, $visit]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('visits._form')
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>{{ __('visit.update') }}
                        </button>
                        <button type="button" onclick="printCurrentPrescription()"
                                class="btn btn-outline-success">
                            <i class="bi bi-printer me-1"></i>{{ __('visit.print_prescription') }}
                        </button>
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function printCurrentPrescription() {
    const rows  = [...document.querySelectorAll('#prescriptionBody tr')];
    const items = rows.map((tr, i) => ({
        no:        i + 1,
        med:       tr.querySelector('[name*="[medication]"]')?.value?.trim() || '',
        method:    tr.querySelector('[name*="[method]"]')?.value || '',
        morning:   tr.querySelector('[name*="[morning]"]')?.value || '',
        afternoon: tr.querySelector('[name*="[afternoon]"]')?.value || '',
        evening:   tr.querySelector('[name*="[evening]"]')?.value || '',
        night:     tr.querySelector('[name*="[night]"]')?.value || '',
        numDay:    tr.querySelector('[name*="[number_day]"]')?.value || '',
        qty:       tr.querySelector('[name*="[quantity]"]')?.value || '',
        unit:      tr.querySelector('[name*="[unit]"]')?.value || '',
        remark:    tr.querySelector('[name*="[remark]"]')?.value || '',
    })).filter(r => r.med);

    if (!items.length) {
        alert('Please fill in at least one medication before printing.');
        return;
    }

    const visitDate = document.querySelector('[name="visit_date"]')?.value || '';
    const doctor    = document.querySelector('[name="doctor_name"]')?.value?.trim() || '';

    const patientName = @json($patient->full_name);
    const patientId   = @json($patient->patient_id);
    const appName     = @json(config('app.name'));

    const tableRows = items.map(r => `<tr>
        <td class="tc">${r.no}</td>
        <td>${r.med}</td>
        <td>${r.method  || '—'}</td>
        <td class="tc">${r.morning   || '—'}</td>
        <td class="tc">${r.afternoon || '—'}</td>
        <td class="tc">${r.evening   || '—'}</td>
        <td class="tc">${r.night     || '—'}</td>
        <td class="tc">${r.numDay    || '—'}</td>
        <td class="tc">${r.qty       || '—'}</td>
        <td>${r.unit   || '—'}</td>
        <td>${r.remark || ''}</td>
    </tr>`).join('');

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
  <table>
    <thead>
      <tr>
        <th style="width:30px">No.</th>
        <th>Medication Name</th>
        <th style="width:90px">Method</th>
        <th style="width:58px" class="tc">Morning</th>
        <th style="width:64px" class="tc">Afternoon</th>
        <th style="width:55px" class="tc">Evening</th>
        <th style="width:48px" class="tc">Night</th>
        <th style="width:62px" class="tc">No. Day</th>
        <th style="width:50px" class="tc">Qty</th>
        <th style="width:68px">Unit</th>
        <th>Remark</th>
      </tr>
    </thead>
    <tbody>${tableRows}</tbody>
  </table>
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
