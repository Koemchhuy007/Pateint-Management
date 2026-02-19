@extends('layouts.app')

@section('title', $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item active">{{ $patient->full_name }}</li>
@endsection

@push('styles')
<style>
    /* ── Info panel (right) ── */
    .info-panel {
        position: sticky;
        top: 116px;
    }

    /* ── Small photo thumbnail ── */
    .patient-photo-thumb {
        width: 80px;
        height: 96px;
        border-radius: 10px;
        overflow: hidden;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        flex-shrink: 0;
    }
    .patient-photo-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .patient-photo-thumb .thumb-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
    }

    /* ── Info table ── */
    .info-table td:first-child {
        width: 40%;
        color: #64748b;
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding-right: 8px;
        vertical-align: top;
    }
    .info-table td:last-child {
        color: #1e293b;
        font-size: .88rem;
    }
    .info-table tr td { padding: 5px 0; border: none; }

    /* ── Section heading ── */
    .section-heading {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #94a3b8;
        padding-bottom: 5px;
        border-bottom: 1px solid #e2e8f0;
        margin: 14px 0 10px;
    }
    .section-heading:first-child { margin-top: 0; }

    /* ── Active (undischarged) visit row ── */
    .visit-active-row {
        background: linear-gradient(90deg, rgba(16,185,129,.08) 0%, rgba(16,185,129,.03) 100%);
        border-left: 4px solid #10b981;
    }
    .visit-active-row td:first-child { padding-left: 10px; }
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
    <div class="col-md-4">
        <div class="info-panel">
            <div class="card">
                <div class="card-body">

                    {{-- Photo + Name side by side --}}
                    <div class="d-flex align-items-start gap-3 mb-3">

                        {{-- Small rectangular photo --}}
                        <div class="patient-photo-thumb">
                            @if($patient->photo_url)
                                <img src="{{ $patient->photo_url }}" alt="{{ $patient->full_name }}">
                            @else
                                <div class="thumb-placeholder">
                                    <i class="bi bi-person-bounding-box" style="font-size:2rem;"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Name + badges --}}
                        <div style="min-width:0;">
                            <h6 class="mb-1 fw-bold" style="line-height:1.3;">{{ $patient->full_name }}</h6>
                            <div class="d-flex gap-1 flex-wrap mb-2">
                                <span class="badge badge-{{ $patient->status }}">{{ ucfirst($patient->status) }}</span>
                                @if($patient->type === 'OPD')
                                    <span class="badge" style="background:#0ea5e9;">OPD</span>
                                @elseif($patient->type === 'IPD')
                                    <span class="badge" style="background:#8b5cf6;">IPD</span>
                                @endif
                                @if($patient->active_case)
                                    <span class="badge bg-success">
                                        <i class="bi bi-folder2-open me-1"></i>Active
                                    </span>
                                @endif
                            </div>
                            {{-- Action buttons inside the card --}}
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                @if($patient->active_case)
                                    <form action="{{ route('patients.case.discard', $patient) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Discharge this case for {{ $patient->full_name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-box-arrow-right me-1"></i>Discharge
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left me-1"></i>Back
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Personal --}}
                    <div class="section-heading">Personal</div>
                    <table class="info-table w-100">
                        <tr><td>Patient ID</td><td><code>{{ $patient->patient_id }}</code></td></tr>
                        <tr><td>Date of Birth</td><td>{{ $patient->date_of_birth->format('d/m/Y') }}</td></tr>
                        <tr><td>Age</td><td>{{ $patient->age ? $patient->age . ' years' : '—' }}</td></tr>
                        <tr><td>Sex</td><td>{{ ucfirst($patient->sex ?? '—') }}</td></tr>
                        <tr><td>Marital</td><td>{{ ucfirst($patient->personal_status ?? '—') }}</td></tr>
                        <tr><td>Blood Type</td><td>{{ $patient->blood_type ?? '—' }}</td></tr>
                    </table>

                    {{-- Contact --}}
                    <div class="section-heading">Contact</div>
                    <table class="info-table w-100">
                        <tr><td>Phone</td><td>{{ $patient->phone ?? '—' }}</td></tr>
                        <tr><td>Email</td><td style="word-break:break-all;">{{ $patient->email ?? '—' }}</td></tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $patient->full_address ?: ($patient->address ?? '—') }}</td>
                        </tr>
                    </table>

                    {{-- Emergency --}}
                    <div class="section-heading">Emergency</div>
                    <table class="info-table w-100">
                        <tr><td>Name</td><td>{{ $patient->emergency_contact_name ?? '—' }}</td></tr>
                        <tr><td>Phone</td><td>{{ $patient->emergency_contact_phone ?? '—' }}</td></tr>
                    </table>

                    @if($patient->insurance_info)
                        <div class="section-heading">Insurance</div>
                        <p class="mb-0" style="font-size:.88rem;">{{ $patient->insurance_info }}</p>
                    @endif

                    @if($patient->medical_notes)
                        <div class="section-heading">Medical Notes</div>
                        <p class="mb-0 text-muted" style="font-size:.85rem;">{{ $patient->medical_notes }}</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════
         RIGHT — Visit History
    ════════════════════════════════ --}}
    <div class="col-md-8">
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
                                    <th>Reason</th>
                                    <th>Diagnosis</th>
                                    <th>Doctor</th>
                                    <th>Follow-up</th>
                                    <th style="width:100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->visits as $i => $visit)
                                @php $isActive = ($i === 0 && $patient->active_case); @endphp
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
                                    <td>{{ Str::limit($visit->reason, 45) }}</td>
                                    <td class="text-muted">{{ $visit->diagnosis ? Str::limit($visit->diagnosis, 45) : '—' }}</td>
                                    <td>{{ $visit->doctor_name }}</td>
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
