@extends('layouts.app')

@section('title', 'Add New Case — ' . $patient->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
<li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
<li class="breadcrumb-item active">Add New Case</li>
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
    width: 120px;
    height: 136px;
    border-radius: 10px;
    overflow: hidden;
    background: #f1f5f9;
    border: 2px solid #e2e8f0;
    flex-shrink: 0;
    margin-top: 50px;
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

.info-table tr td {
    padding: 5px 0;
    border: none;
}

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

.section-heading:first-child {
    margin-top: 0;
}
</style>
@endpush

@section('content')

{{-- ── Two-column layout ── --}}
<div class="row g-4 align-items-start">

    {{-- ════════════════════════════════
         LEFT — Patient Info Panel
    ════════════════════════════════ --}}
    <div class="col-md-3">
        <div class="info-panel">
            <div class="card">
                <div class="card-body">
                    {{-- Personal --}}
                    <div class="row">
                        <div class="col-md-9">
                            <div class="section-heading">Personal</div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Photo + Name side by side --}}
                    <div class="row g-4 align-items-start">
                        <div class="col-md-8">
                            <table class="info-table w-100">
                                <tr>
                                    <td>Patient Name</td>
                                    <td><code>{{ $patient->full_name }}</code></td>
                                </tr>
                                <tr>
                                    <td>Patient ID</td>
                                    <td><code>{{ $patient->patient_id }}</code></td>
                                </tr>
                                <tr>
                                    <td>Date of Birth</td>
                                    <td>{{ $patient->date_of_birth->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Age</td>
                                    <td>{{ $patient->age ? $patient->age . ' years' : '—' }}</td>
                                </tr>
                                <tr>
                                    <td>Sex</td>
                                    <td>{{ ucfirst($patient->sex ?? '—') }}</td>
                                </tr>
                                <tr>
                                    <td>Marital</td>
                                    <td>{{ ucfirst($patient->personal_status ?? '—') }}</td>
                                </tr>
                                <tr>
                                    <td>Blood Type</td>
                                    <td>{{ $patient->blood_type ?? '—' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
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
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="section-heading">Contact</div>
                    <table class="info-table w-100">
                        <tr>
                            <td>Phone</td>
                            <td>{{ $patient->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td style="word-break:break-all;">{{ $patient->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $patient->full_address ?: ($patient->address ?? '—') }}</td>
                        </tr>
                    </table>

                    {{-- Emergency --}}
                    <div class="section-heading">Emergency</div>
                    <table class="info-table w-100">
                        <tr>
                            <td>Name</td>
                            <td>{{ $patient->emergency_contact_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $patient->emergency_contact_phone ?? '—' }}</td>
                        </tr>
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
         RIGHT — Add New Case
    ════════════════════════════════ --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-clipboard-plus me-2"></i>Add New Case
                </span>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.visits.store', $patient) }}" method="POST">
                    @csrf
                    @include('visits._form')
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Visit
                        </button>
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
