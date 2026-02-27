@extends('layouts.app')
@section('title', __('admin.general_settings'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('admin.dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('nav.setting') }}</li>
@endsection

@section('content')
<div class="row g-4 align-items-start">

    @include('admin.settings._sidebar')

    <div class="col-lg-10 col-md-9">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-sliders" style="color:#f59e0b;font-size:1.4rem;"></i>
            <div>
                <h5 class="mb-0 fw-bold">{{ __('admin.general_settings') }}</h5>
                <small class="text-muted">{{ __('admin.general_settings_sub') }}</small>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf @method('PUT')

            {{-- ── Application ── --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-app-indicator text-primary"></i>
                    <span>{{ __('admin.app_section') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                {{ __('admin.app_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="app_name"
                                   class="form-control @error('app_name') is-invalid @enderror"
                                   value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}"
                                   required maxlength="100">
                            <div class="form-text">{{ __('admin.app_name_help') }}</div>
                            @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('admin.app_description') }}</label>
                            <input type="text" name="app_description"
                                   class="form-control @error('app_description') is-invalid @enderror"
                                   value="{{ old('app_description', $settings['app_description'] ?? '') }}"
                                   maxlength="255"
                                   placeholder="{{ __('placeholder.app_description') }}">
                            <div class="form-text">{{ __('admin.app_description_help') }}</div>
                            @error('app_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Localisation ── --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-globe text-primary"></i>
                    <span>{{ __('admin.locale_section') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                {{ __('admin.default_language') }} <span class="text-danger">*</span>
                            </label>
                            <select name="default_locale"
                                    class="form-select @error('default_locale') is-invalid @enderror">
                                <option value="km" {{ ($settings['default_locale'] ?? 'km') === 'km' ? 'selected' : '' }}>
                                    🇰🇭 ខ្មែរ — Khmer
                                </option>
                                <option value="en" {{ ($settings['default_locale'] ?? 'km') === 'en' ? 'selected' : '' }}>
                                    🇬🇧 English
                                </option>
                            </select>
                            <div class="form-text">
                                {{ __('admin.locale_help') }}
                            </div>
                            @error('default_locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                {{ __('admin.timezone') }} <span class="text-danger">*</span>
                            </label>
                            <select name="app_timezone"
                                    class="form-select @error('app_timezone') is-invalid @enderror">
                                @foreach($timezones as $tz => $label)
                                <option value="{{ $tz }}"
                                    {{ ($settings['app_timezone'] ?? 'Asia/Phnom_Penh') === $tz ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                {{ __('admin.timezone_help') }}
                            </div>
                            @error('app_timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Info box ── --}}
            <div class="alert alert-warning d-flex gap-2 align-items-start mb-3" style="font-size:.84rem;">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                <div>
                    <strong>{{ __('common.note') }}:</strong> {{ __('admin.settings_note') }}
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning px-4 text-white fw-semibold">
                    <i class="bi bi-check-lg me-1"></i>{{ __('admin.save_settings') }}
                </button>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
            </div>

        </form>

    </div>
</div>
@endsection
