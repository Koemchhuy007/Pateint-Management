@extends('layouts.app')

@section('title', __('admin.dashboard'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('admin.dashboard') }}</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-shield-lock-fill" style="color:#f59e0b;font-size:1.4rem;"></i>
        <h4 class="mb-0 fw-bold">{{ __('admin.dashboard') }}</h4>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.system-admins.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-person-badge me-1"></i>{{ __('admin.add_system_admin') }}
        </a>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-building-add me-1"></i>{{ __('admin.add_client') }}
        </a>
        <a href="{{ route('analytics.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-graph-up-arrow me-1"></i>{{ __('nav.analytics') }}
        </a>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#2563eb;">{{ $stats['clients'] }}</div>
            <div class="text-muted" style="font-size:.82rem;">{{ __('admin.stats_clients') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#059669;">{{ $stats['users'] }}</div>
            <div class="text-muted" style="font-size:.82rem;">{{ __('admin.stats_total_users') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#7c3aed;">{{ $stats['patients'] }}</div>
            <div class="text-muted" style="font-size:.82rem;">{{ __('admin.stats_patients') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div style="font-size:2rem;font-weight:700;color:#d97706;">{{ $stats['invoices'] }}</div>
            <div class="text-muted" style="font-size:.82rem;">{{ __('admin.stats_invoices') }}</div>
        </div>
    </div>
</div>

{{-- ── Recent Clients ── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-buildings me-2"></i>{{ __('admin.recent_clients') }}</span>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-grid me-1"></i>{{ __('admin.all_clients_link') }}
        </a>
    </div>
    <div class="card-body p-0">
        @if($clients->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-building display-4 d-block mb-3"></i>
            <p>{{ __('admin.no_clients_yet') }}</p>
            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_first_client') }}
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('admin.client_name') }}</th>
                        <th>{{ __('field.email') }}</th>
                        <th class="text-center">{{ __('admin.stats_total_users') }}</th>
                        <th class="text-center">{{ __('field.status') }}</th>
                        <th>{{ __('common.created') }}</th>
                        <th style="width:100px" class="text-center">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                    <tr>
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td class="text-muted">{{ $client->email ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $client->users_count }}</span>
                        </td>
                        <td class="text-center">
                            @if($client->is_active)
                            <span class="badge" style="background:#059669;">{{ __('common.active') }}</span>
                            @else
                            <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                            {{ $client->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.clients.edit', $client) }}"
                               class="btn btn-sm btn-outline-primary border-0">
                                <i class="bi bi-pencil"></i>
                            </a>
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
