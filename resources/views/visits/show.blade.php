@extends('layouts.app')

@section('title', 'Visit — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">{{ $visit->visit_date->format('d/m/Y') }}</li>
@endsection

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
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('patients.visits.edit', [$patient, $visit]) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- ── Visit Details ── --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th width="40%">Visit Date</th><td>{{ $visit->visit_date->format('d/m/Y H:i') }}</td></tr>
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
                                        <span class="badge bg-success">Active Case</span>
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
                <div class="fw-semibold mb-2" style="color:#16a34a;font-size:.95rem;">
                    <i class="bi bi-capsule-pill me-1"></i>Doctor's Prescription
                </div>

                @if(!empty($visit->prescription))
                <div class="table-responsive">
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
