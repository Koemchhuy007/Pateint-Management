@extends('layouts.app')
@section('title', 'Users — Settings')

@section('breadcrumb')
<li class="breadcrumb-item active">Settings</li>
<li class="breadcrumb-item active">Users</li>
@endsection

@push('styles')
<style>
.role-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600; }
.role-system_admin { background:#ede9fe; color:#7c3aed; }
.role-doctor       { background:#dcfce7; color:#15803d; }
.role-cashier      { background:#fff7ed; color:#c2410c; }
</style>
@endpush

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0" style="color:#1e293b;">
                    <i class="bi bi-people-fill me-2 text-primary"></i>Users
                </h5>
                <div class="text-muted mt-1" style="font-size:.82rem;">
                    {{ $users->count() }} user{{ $users->count() != 1 ? 's' : '' }}
                </div>
            </div>
            <a href="{{ route('settings.users.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add User
            </a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:.88rem;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th class="ps-4" style="width:50px;">#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th style="width:130px;">Role</th>
                            <th style="width:110px;" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                        <tr>
                            <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold" style="color:#1e293b;">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <span class="badge bg-secondary ms-1" style="font-size:.65rem;">You</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $user->username ?? '—' }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span class="role-badge role-{{ $user->role }}">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('settings.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('settings.users.destroy', $user) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete user {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                No users yet.
                                <a href="{{ route('settings.users.create') }}">Add one →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
