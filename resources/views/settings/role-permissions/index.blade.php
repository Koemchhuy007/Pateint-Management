@extends('layouts.app')
@section('title', 'Role Permissions — Settings')

@section('breadcrumb')
<li class="breadcrumb-item active">Settings</li>
<li class="breadcrumb-item active">Role Permissions</li>
@endsection

@push('styles')
<style>
.perm-table th, .perm-table td { vertical-align: middle; }

.perm-table thead th.role-col {
    text-align: center;
    min-width: 130px;
}

/* Feature icon chip */
.feature-chip {
    display: inline-flex; align-items: center; gap: 7px;
    font-weight: 600; font-size: .88rem; color: #1e293b;
}
.feature-chip .chip-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}

/* Custom toggle checkbox */
.perm-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
}
.perm-toggle input[type="checkbox"] {
    width: 42px; height: 24px;
    appearance: none; -webkit-appearance: none;
    background: #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    position: relative;
    transition: background .2s;
    flex-shrink: 0;
}
.perm-toggle input[type="checkbox"]::after {
    content: '';
    position: absolute;
    top: 3px; left: 3px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
    transition: left .2s;
}
.perm-toggle input[type="checkbox"]:checked {
    background: #2563eb;
}
.perm-toggle input[type="checkbox"]:checked::after {
    left: 21px;
}
.perm-toggle input[type="checkbox"]:disabled {
    background: #93c5fd;
    cursor: not-allowed;
    opacity: .7;
}

/* Role header badge */
.role-header-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: .78rem; font-weight: 600;
}
.badge-system_admin { background:#ede9fe; color:#7c3aed; }
.badge-doctor       { background:#dcfce7; color:#15803d; }
.badge-cashier      { background:#fff7ed; color:#c2410c; }
</style>
@endpush

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h5 class="fw-bold mb-1" style="color:#1e293b;">
                    <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Role Permissions
                </h5>
                <p class="text-muted mb-0" style="font-size:.83rem;">
                    Control which menu features each role can access.
                    System Admin always has full access.
                </p>
            </div>
        </div>

        <form action="{{ route('settings.role-permissions.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="card">
                <div class="table-responsive">
                    <table class="table perm-table align-middle mb-0" style="font-size:.88rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="ps-4" style="width:220px;">Menu Feature</th>
                                @foreach($roles as $roleKey => $roleLabel)
                                <th class="role-col text-center">
                                    <span class="role-header-badge badge-{{ $roleKey }}">
                                        {{ $roleLabel }}
                                    </span>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $featureIcons = [
                                'patients'  => ['icon' => 'bi-people-fill',          'bg' => '#eff6ff', 'color' => '#2563eb'],
                                'invoice'   => ['icon' => 'bi-receipt-cutoff',        'bg' => '#f0fdf4', 'color' => '#16a34a'],
                                'drugstore' => ['icon' => 'bi-capsule-pill',          'bg' => '#fdf4ff', 'color' => '#9333ea'],
                                'reports'   => ['icon' => 'bi-bar-chart-line-fill',   'bg' => '#fff7ed', 'color' => '#ea580c'],
                                'settings'  => ['icon' => 'bi-gear-fill',             'bg' => '#f8fafc', 'color' => '#475569'],
                            ];
                            @endphp

                            @foreach($features as $featureKey => $featureLabel)
                            <tr>
                                <td class="ps-4">
                                    <div class="feature-chip">
                                        <span class="chip-icon"
                                              style="background:{{ $featureIcons[$featureKey]['bg'] }};
                                                     color:{{ $featureIcons[$featureKey]['color'] }};">
                                            <i class="bi {{ $featureIcons[$featureKey]['icon'] }}"></i>
                                        </span>
                                        {{ $featureLabel }}
                                    </div>
                                </td>

                                @foreach($roles as $roleKey => $roleLabel)
                                <td>
                                    <div class="perm-toggle">
                                        @if($roleKey === 'system_admin')
                                            {{-- System admin always on, cannot be changed --}}
                                            <input type="checkbox" checked disabled title="System Admin always has full access">
                                        @else
                                            <input type="checkbox"
                                                   name="permissions[{{ $roleKey }}][{{ $featureKey }}]"
                                                   value="1"
                                                   {{ !empty($permissions[$roleKey][$featureKey]) ? 'checked' : '' }}>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                    <p class="text-muted mb-0" style="font-size:.78rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Changes take effect immediately on next page load.
                    </p>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Save Permissions
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
