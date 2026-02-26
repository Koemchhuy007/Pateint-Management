@extends('layouts.app')

@section('title', 'Add System Admin — Super Admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Super Admin</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.system-admins.index') }}">System Admins</a></li>
<li class="breadcrumb-item active">Add System Admin</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-person-badge-fill me-2" style="color:#1d4ed8;"></i>
                    Create New System Admin
                </span>
                <a href="{{ route('admin.system-admins.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">

                <div class="alert" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;font-size:.83rem;" role="alert">
                    <i class="bi bi-info-circle me-1"></i>
                    System admins can access Analytics and manage their assigned client's settings.
                    They <strong>cannot</strong> create other system admins or access the Super Admin panel.
                </div>

                <form action="{{ route('admin.system-admins.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Dr. Anna Smith" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username') }}" placeholder="e.g. anna.smith" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="anna@clinic.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Assign to Client <span class="text-muted fw-normal">(optional)</span></label>
                        <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                            <option value="">— Platform-wide (no client) —</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Assign to a client so this admin manages only that client's data.
                        </div>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Create System Admin
                        </button>
                        <a href="{{ route('admin.system-admins.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
