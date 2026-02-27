@extends('layouts.app')

@section('title', __('admin.system_admins'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('admin.dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('admin.system_admins') }}</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-person-badge-fill me-2" style="color:#1d4ed8;"></i>{{ __('admin.system_admins') }}
            <span class="badge bg-secondary ms-1">{{ $systemAdmins->total() }}</span>
        </span>
        <a href="{{ route('admin.system-admins.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_system_admin') }}
        </a>
    </div>
    <div class="card-body p-0">
        @if($systemAdmins->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-person-x display-4 d-block mb-3"></i>
            <p>{{ __('admin.no_system_admins') }}</p>
            <a href="{{ route('admin.system-admins.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_system_admin') }}
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('field.name') }}</th>
                        <th>{{ __('field.username') }}</th>
                        <th>{{ __('field.email') }}</th>
                        <th>{{ __('admin.assigned_client') }}</th>
                        <th>{{ __('common.created') }}</th>
                        <th style="width:90px" class="text-center">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($systemAdmins as $i => $sa)
                    <tr>
                        <td class="text-muted small">{{ $systemAdmins->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $sa->name }}</td>
                        <td>
                            <code class="text-muted" style="font-size:.8rem;">{{ $sa->username }}</code>
                        </td>
                        <td class="text-muted">{{ $sa->email ?: '—' }}</td>
                        <td>
                            @if($sa->client)
                            <span class="badge" style="background:#0284c7;">{{ $sa->client->name }}</span>
                            @else
                            <span class="text-muted fst-italic" style="font-size:.8rem;">{{ __('admin.platform_wide_badge') }}</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                            {{ $sa->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.system-admins.destroy', $sa) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete system admin \'{{ addslashes($sa->name) }}\'?')">
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
            {{ $systemAdmins->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
