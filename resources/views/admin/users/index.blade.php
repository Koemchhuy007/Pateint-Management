@extends('layouts.app')

@section('title', 'All Users — Super Admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Super Admin</a></li>
<li class="breadcrumb-item active">Users</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-people-fill me-2"></i>All Users
            <span class="badge bg-secondary ms-1">{{ $users->total() }}</span>
        </span>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="d-flex gap-2 flex-wrap align-items-center">
            <select name="client_id" class="form-select form-select-sm" style="width:180px;"
                    onchange="this.form.submit()">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
                @endforeach
            </select>
            <div class="input-group input-group-sm" style="width:220px;">
                <input type="text" name="search" class="form-control"
                       placeholder="Search name / username"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            @if(request()->hasAny(['client_id','search']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg"></i> Clear
            </a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        @if($users->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-people display-4 d-block mb-3"></i>
            <p>No users found.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Client</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $user)
                    <tr>
                        <td class="text-muted small">{{ $users->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->username }}</td>
                        <td class="text-muted">{{ $user->email ?: '—' }}</td>
                        <td>
                            <span class="badge
                                {{ $user->role === 'system_admin' ? 'bg-danger' : ($user->role === 'doctor' ? 'bg-primary' : 'bg-secondary') }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td>
                            @if($user->client)
                            <span class="text-muted">{{ $user->client->name }}</span>
                            @else
                            <span class="text-muted fst-italic">No tenant</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.users.impersonate', $user) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Impersonate {{ addslashes($user->name) }}?')">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm btn-outline-warning border-0"
                                        title="Impersonate this user">
                                    <i class="bi bi-person-badge"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
