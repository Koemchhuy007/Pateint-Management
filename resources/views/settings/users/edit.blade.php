@extends('layouts.app')
@section('title', 'Edit User — Settings')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('settings.users.index') }}">Settings / Users</a></li>
<li class="breadcrumb-item active">Edit User</li>
@endsection

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil me-2 text-primary"></i>Edit User — {{ $user->name }}</span>
                <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('settings.users.update', $user) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-3">

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('field.role') }} <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                @foreach($availableRoles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>
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
                                       value="{{ old('username', $user->username) }}" required>
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
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            @error('email')<div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <hr class="my-1">
                            <p class="text-muted mb-3" style="font-size:.82rem;">
                                <i class="bi bi-lock me-1"></i>
                                {{ __('settings.password_hint') }}
                            </p>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('settings.new_password') }}</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('settings.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Re-enter new password">
                        </div>

                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i>{{ __('settings.update_user') }}
                            </button>
                            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Danger zone (can't delete yourself) --}}
        @if($user->id !== auth()->id())
        <div class="card mt-3" style="border-color:#fecaca !important;">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="fw-semibold text-danger" style="font-size:.88rem;">{{ __('settings.delete_user_title') }}</div>
                    <div class="text-muted" style="font-size:.78rem;">{{ __('common.cannot_undo') }}</div>
                </div>
                <form action="{{ route('settings.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>{{ __('common.delete') }}
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
