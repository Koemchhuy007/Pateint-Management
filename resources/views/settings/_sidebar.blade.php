@push('styles')
<style>
.settings-nav {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    position: sticky;
    top: 116px;
}
.settings-nav-header {
    padding: 14px 16px 10px;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #94a3b8;
    border-bottom: 1px solid #f1f5f9;
}
.settings-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    font-size: .85rem;
    font-weight: 500;
    color: #475569;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all .15s;
    border-bottom: 1px solid #f8fafc;
}
.settings-nav-item:last-child { border-bottom: none; }
.settings-nav-item:hover {
    background: #f8fafc;
    color: #1e293b;
}
.settings-nav-item.active {
    background: #eff6ff;
    color: #2563eb;
    border-left-color: #2563eb;
    font-weight: 600;
}
.settings-nav-item i { font-size: .95rem; width: 18px; text-align: center; }
</style>
@endpush

<div class="col-lg-2 col-md-3">
    <nav class="settings-nav">
        <div class="settings-nav-header">
            <i class="bi bi-gear-fill me-1"></i> Settings
        </div>

        <a href="{{ route('settings.users.index') }}"
           class="settings-nav-item {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> {{ __('settings.sidebar.users') }}
        </a>

        <a href="{{ route('settings.role-permissions.index') }}"
           class="settings-nav-item {{ request()->routeIs('settings.role-permissions.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill"></i> {{ __('settings.sidebar.role_permissions') }}
        </a>

        <a href="{{ route('settings.payment-types.index') }}"
           class="settings-nav-item {{ request()->routeIs('settings.payment-types.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card-fill"></i> {{ __('settings.sidebar.payment_types') }}
        </a>

        <a href="{{ route('settings.service-groups.index') }}"
           class="settings-nav-item {{ request()->routeIs('settings.service-groups.*') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> {{ __('settings.sidebar.service_groups') }}
        </a>

    </nav>
</div>
