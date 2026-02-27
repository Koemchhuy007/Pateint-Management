@extends('layouts.app')
@section('title', 'Add User — Settings')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('settings.users.index') }}">Settings / Users</a></li>
<li class="breadcrumb-item active">Add User</li>
@endsection

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-plus me-2 text-primary"></i>{{ __('settings.add_new_user') }}</span>
                <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('settings.users.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.role') }} <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">{{ __('placeholder.select_role') }}</option>
                                @foreach($availableRoles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.username') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-at"></i>
                                </span>
                                <input type="text" name="username"
                                       class="form-control border-start-0 @error('username') is-invalid @enderror"
                                       value="{{ old('username') }}"
                                       placeholder="e.g. dr_john" required>
                            </div>
                            @error('username')<div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.email') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email"
                                       class="form-control border-start-0 @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="user@example.com" required>
                            </div>
                            @error('email')<div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.password_confirm') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Re-enter password" required>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i>{{ __('settings.create_user') }}
                            </button>
                            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
