@extends('layouts.app')

@section('title', 'Invoice')

@section('breadcrumb')
    <li class="breadcrumb-item active">Invoice</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-receipt-cutoff me-2 text-primary"></i><strong>Active Patients</strong></span>
        <span class="badge bg-primary rounded-pill">{{ $patients->count() }} patient(s)</span>
    </div>

    @if($patients->isEmpty())
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-person-check fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-0">No active patients &mdash; all visits have been discharged.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:130px">Patient ID</th>
                        <th>Patient Name</th>
                        <th style="width:80px" class="text-center">Type</th>
                        <th style="width:130px">Visit Date</th>
                        <th>Doctor</th>
                        <th style="width:90px" class="text-center">Invoices</th>
                        <th style="width:110px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        @php $visit = $patient->visits->first(); @endphp
                        <tr>
                            <td><code class="text-primary">{{ $patient->patient_id }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $patient->full_name }}</div>
                                @if($patient->phone)
                                    <small class="text-muted">{{ $patient->phone }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($visit)
                                    <span class="badge bg-{{ $visit->visit_type === 'IPD' ? 'warning text-dark' : 'info text-dark' }}">
                                        {{ $visit->visit_type }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $visit?->visit_date->format('d/m/Y') }}
                            </td>
                            <td>{{ $visit?->doctor_name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill">{{ $patient->invoices_count }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('invoices.show', $patient) }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-receipt me-1"></i>Invoice
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
