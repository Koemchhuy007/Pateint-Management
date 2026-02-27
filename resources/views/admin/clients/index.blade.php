@extends('layouts.app')

@section('title', __('admin.clients'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('admin.dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('admin.clients') }}</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-buildings me-2"></i>{{ __('admin.stats_clients') }}
            <span class="badge bg-secondary ms-1">{{ $clients->total() }}</span>
        </span>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_client') }}
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
                        <th>#</th>
                        <th>{{ __('admin.client_name') }}</th>
                        <th>{{ __('field.email') }}</th>
                        <th>{{ __('field.phone') }}</th>
                        <th class="text-center">{{ __('admin.stats_total_users') }}</th>
                        <th class="text-center">{{ __('field.status') }}</th>
                        <th>{{ __('common.created') }}</th>
                        <th style="width:110px" class="text-center">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $i => $client)
                    <tr>
                        <td class="text-muted small">{{ $clients->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td class="text-muted">{{ $client->email ?: '—' }}</td>
                        <td class="text-muted">{{ $client->phone ?: '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.users.index', ['client_id' => $client->id]) }}"
                               class="badge bg-secondary text-decoration-none">
                                {{ $client->users_count }}
                            </a>
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
                               class="btn btn-sm btn-outline-primary border-0"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.clients.destroy', $client) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete client \'{{ addslashes($client->name) }}\'? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger border-0"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
