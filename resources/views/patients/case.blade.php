@extends('layouts.app')

@section('title', 'Case — ' . $patient->full_name)

@section('content')

{{-- Patient Header --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h4 class="mb-1"><i class="bi bi-person-vcard me-2 text-primary"></i>{{ $patient->full_name }}</h4>
                <div class="text-muted d-flex flex-wrap gap-3">
                    <span><i class="bi bi-credit-card me-1"></i>{{ $patient->patient_id }}</span>
                    <span><i class="bi bi-calendar3 me-1"></i>{{ $patient->date_of_birth->format('M d, Y') }} ({{ $patient->age }} yrs)</span>
                    <span><i class="bi bi-gender-ambiguous me-1"></i>{{ ucfirst($patient->sex) }}</span>
                    @if($patient->blood_type)
                        <span><i class="bi bi-droplet me-1"></i>{{ $patient->blood_type }}</span>
                    @endif
                    @if($patient->phone)
                        <span><i class="bi bi-telephone me-1"></i>{{ $patient->phone }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge badge-{{ $patient->status }} fs-6 px-3 py-2">{{ ucfirst($patient->status) }}</span>
                <a href="{{ route('patients.visits.create', $patient) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('visit.create') }}
                </a>
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Visit History --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard-pulse me-2"></i>{{ __('patient.visit_history') }}
            <span class="badge bg-secondary ms-1">{{ $patient->visits->count() }}</span>
        </span>
    </div>
    <div class="card-body p-0">
        @if($patient->visits->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <p class="mb-2">{{ __('visit.no_visits_yet') }}</p>
                <a href="{{ route('patients.visits.create', $patient) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('visit.create') }}
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('visit.visit_date') }}</th>
                            <th>Reason</th>
                            <th>{{ __('visit.diagnosis') }}</th>
                            <th>Doctor</th>
                            <th>Follow-up</th>
                            <th width="120">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patient->visits as $visit)
                        <tr>
                            <td>
                                <strong>{{ $visit->visit_date->format('M d, Y') }}</strong>
                                <br><small class="text-muted">{{ $visit->visit_date->format('H:i') }}</small>
                            </td>
                            <td>{{ Str::limit($visit->reason, 50) }}</td>
                            <td class="text-muted">{{ $visit->diagnosis ? Str::limit($visit->diagnosis, 50) : '—' }}</td>
                            <td>{{ $visit->doctor_name }}</td>
                            <td>
                                @if($visit->follow_up_date)
                                    <span class="{{ $visit->follow_up_date->isPast() ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $visit->follow_up_date->format('M d, Y') }}
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
                                          onsubmit="return confirm('{{ __('visit.delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="{{ __('common.delete') }}">
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

@endsection
