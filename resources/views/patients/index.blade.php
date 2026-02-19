@extends('layouts.app')

@section('title', 'All Patients')

@section('breadcrumb')
<li class="breadcrumb-item active">Patients</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-people me-2"></i>Patient Records</span>
        <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Patient
        </a>
    </div>
    <div class="card-body">

        {{-- Search / Filter --}}
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by name, ID, phone..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">All types</option>
                    <option value="OPD" {{ request('type') === 'OPD' ? 'selected' : '' }}>OPD</option>
                    <option value="IPD" {{ request('type') === 'IPD' ? 'selected' : '' }}>IPD</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:50px">#</th>
                        <th>Last Visit</th>
                        <th>Patient Name</th>
                        <th class="text-center">Sex</th>
                        <th class="text-center">Age</th>
                        <th>Patient Code</th>
                        <th class="text-center">Type</th>
                        <th>Address</th>
                        <th class="text-center" style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            {{-- Row number --}}
                            <td class="text-center text-muted small">
                                {{ $patients->firstItem() + $loop->index }}
                            </td>

                            {{-- Latest visit date --}}
                            <td>
                                @if($patient->visits_max_visit_date)
                                    <span class="text-dark">
                                        {{ \Carbon\Carbon::parse($patient->visits_max_visit_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Patient Name --}}
                            <td>
                                <div class="fw-semibold">{{ $patient->full_name }}</div>
                                @if($patient->active_case)
                                    <span class="badge bg-warning text-dark" style="font-size:.62rem;">Active Case</span>
                                @endif
                            </td>

                            {{-- Sex --}}
                            <td class="text-center">
                                @if($patient->sex === 'male')
                                    <i class="bi bi-gender-male text-primary" title="Male"></i>
                                @elseif($patient->sex === 'female')
                                    <i class="bi bi-gender-female text-danger" title="Female"></i>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Age --}}
                            <td class="text-center">
                                {{ $patient->age ?? '—' }}
                            </td>

                            {{-- Patient Code --}}
                            <td>
                                <code class="text-dark">{{ $patient->patient_id }}</code>
                            </td>

                            {{-- Type --}}
                            <td class="text-center">
                                @if($patient->type === 'OPD')
                                    <span class="badge" style="background:#0ea5e9;">OPD</span>
                                @elseif($patient->type === 'IPD')
                                    <span class="badge" style="background:#8b5cf6;">IPD</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Address --}}
                            <td>
                                @php
                                    $parts = array_filter([
                                        $patient->village?->name,
                                        $patient->community?->name,
                                        $patient->district?->name,
                                        $patient->province?->name,
                                    ]);
                                    $addr = $patient->address
                                        ? $patient->address . (count($parts) ? ', ' . implode(', ', $parts) : '')
                                        : implode(', ', $parts);
                                @endphp
                                @if($addr)
                                    <small class="text-muted">{{ $addr }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('patients.show', $patient) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                No patients found.
                                <a href="{{ route('patients.create') }}">Add your first patient</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
