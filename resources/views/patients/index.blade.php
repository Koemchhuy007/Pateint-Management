@extends('layouts.app')

@section('title', __('patient.title'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('patient.title') }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-people me-2"></i>{{ __('patient.title') }}</span>
        <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ __('patient.create') }}
        </a>
    </div>
    <div class="card-body">

        {{-- Search / Filter --}}
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control"
                       placeholder="{{ __('placeholder.patient_search') }}"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">{{ __('common.all') }}</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>{{ __('patient.active_case') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('patient.discharge') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>{{ __('common.search') }}
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:50px">#</th>
                        <th>{{ __('field.last_visit') }}</th>
                        <th>{{ __('patient.title') }}</th>
                        <th class="text-center">{{ __('field.status') }}</th>
                        <th class="text-center">{{ __('field.sex') }}</th>
                        <th class="text-center">{{ __('field.age') }}</th>
                        <th>{{ __('field.address') }}</th>
                        <th class="text-center" style="width:120px">{{ __('common.actions') }}</th>
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
                                <div class="text-muted" style="font-size:.75rem;">{{ $patient->patient_id }}</div>
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($patient->undischarged_count > 0)
                                    <span class="badge bg-success">{{ __('patient.active_case') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('patient.discharge') }}</span>
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
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                {{ __('patient.no_found') }}
                                <a href="{{ route('patients.create') }}">{{ __('patient.add_first') }}</a>
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
