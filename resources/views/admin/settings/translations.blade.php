@extends('layouts.app')
@section('title', __('admin.language_translations'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('admin.dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('nav.setting') }}</a></li>
<li class="breadcrumb-item active">{{ __('admin.language_translations') }}</li>
@endsection

@push('styles')
<style>
.group-header {
    background: #fffbeb;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #92400e;
    padding: 8px 16px;
    border-top: 1px solid #fde68a;
}
.group-header:first-child { border-top: none; }

.trans-row {
    display: grid;
    grid-template-columns: 220px 1fr 1fr;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
}
.trans-row:last-child { border-bottom: none; }

.trans-key {
    padding: 8px 16px;
    font-size: .75rem;
    color: #94a3b8;
    font-family: monospace;
    border-right: 1px solid #f1f5f9;
    align-self: stretch;
    display: flex;
    align-items: center;
}
.trans-en {
    padding: 8px 16px;
    font-size: .85rem;
    color: #374151;
    border-right: 1px solid #f1f5f9;
    align-self: stretch;
    display: flex;
    align-items: center;
    background: #fafafa;
}
.trans-km input {
    width: 100%;
    border: none;
    border-radius: 0;
    padding: 8px 16px;
    font-size: .88rem;
    background: #fff;
    color: #1e293b;
    outline: none;
    transition: background .12s;
}
.trans-km input:focus {
    background: #fffbeb;
    box-shadow: inset 0 0 0 2px #fde68a;
}

.col-header {
    display: grid;
    grid-template-columns: 220px 1fr 1fr;
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    font-size: .75rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.col-header > div {
    padding: 10px 16px;
    border-right: 1px solid #e2e8f0;
}
.col-header > div:last-child { border-right: none; }
</style>
@endpush

@section('content')
<div class="row g-4 align-items-start">

    @include('admin.settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-translate me-2" style="color:#f59e0b;"></i>{{ __('admin.language_translations') }}
                </h5>
                <small class="text-muted">{{ __('admin.translations_help') }}</small>
            </div>
            <span class="badge rounded-pill px-3 py-2"
                  style="background:{{ app()->getLocale() === 'km' ? '#0891b2' : '#2563eb' }};font-size:.8rem;">
                {{ app()->getLocale() === 'km' ? '🇰🇭 ខ្មែរ Active' : '🇬🇧 English Active' }}
            </span>
        </div>

        <form action="{{ route('admin.settings.translations.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="card" style="overflow:hidden;">

                <div class="col-header">
                    <div>{{ __('admin.translation_key') }}</div>
                    <div>🇬🇧 {{ __('admin.english_readonly') }}</div>
                    <div>🇰🇭 {{ __('admin.khmer_editable') }}</div>
                </div>

                @php
                    $groupLabels = [
                        'nav'       => 'Navigation',
                        'settings'  => 'Settings Sidebar',
                        'lang'      => 'Language UI',
                        'common'    => 'Common Buttons & Labels',
                        'field'     => 'Form Fields',
                        'role'      => 'Roles',
                        'gender'    => 'Gender',
                        'patient'   => 'Patients',
                        'visit'     => 'Visits',
                        'invoice'   => 'Invoices',
                        'drug'      => 'Drugstore',
                        'report'    => 'Reports',
                        'analytics' => 'Analytics',
                        'auth'      => 'Login / Auth',
                    ];
                @endphp

                @foreach($groups as $prefix => $rows)
                <div class="group-header">{{ $groupLabels[$prefix] ?? ucfirst($prefix) }}</div>
                @foreach($rows as $key => ['en' => $enVal, 'km' => $kmVal])
                <div class="trans-row">
                    <div class="trans-key">{{ $key }}</div>
                    <div class="trans-en">{{ $enVal }}</div>
                    <div class="trans-km">
                        <input type="text"
                               name="km[{{ $key }}]"
                               value="{{ old('km.' . $key, $kmVal) }}"
                               placeholder="{{ $enVal }}">
                    </div>
                </div>
                @endforeach
                @endforeach

            </div>

            <div style="position:sticky;bottom:0;background:#fff;border-top:1px solid #e2e8f0;
                        padding:12px 0;margin-top:16px;display:flex;gap:10px;z-index:100;">
                <button type="submit" class="btn btn-warning px-4 text-white fw-semibold">
                    <i class="bi bi-check-lg me-1"></i>{{ __('admin.save_translations') }}
                </button>
                <a href="{{ route('admin.settings.translations.index') }}" class="btn btn-outline-secondary">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
