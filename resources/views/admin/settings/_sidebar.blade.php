@push('styles')
<style>
.admin-settings-nav {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    position: sticky;
    top: 116px;
}
.admin-settings-nav-header {
    padding: 14px 16px 10px;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #f59e0b;
    border-bottom: 1px solid #fef9c3;
    background: #fffbeb;
    display: flex;
    align-items: center;
    gap: 6px;
}
.admin-settings-nav-item {
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
.admin-settings-nav-item:last-child { border-bottom: none; }
.admin-settings-nav-item:hover {
    background: #fffbeb;
    color: #1e293b;
}
.admin-settings-nav-item.active {
    background: #fffbeb;
    color: #d97706;
    border-left-color: #f59e0b;
    font-weight: 600;
}
.admin-settings-nav-item i { font-size: .95rem; width: 18px; text-align: center; }
</style>
@endpush

<div class="col-lg-2 col-md-3">
    <nav class="admin-settings-nav">
        <div class="admin-settings-nav-header">
            <i class="bi bi-shield-lock-fill"></i> Admin Settings
        </div>

        <a href="{{ route('admin.settings.index') }}"
           class="admin-settings-nav-item {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.update') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i> General
        </a>

        <a href="{{ route('admin.settings.translations.index') }}"
           class="admin-settings-nav-item {{ request()->routeIs('admin.settings.translations.*') ? 'active' : '' }}">
            <i class="bi bi-translate"></i> Language & Translations
        </a>
    </nav>
</div>
