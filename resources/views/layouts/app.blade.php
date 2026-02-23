<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Patient Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --topbar-bg:     #1a2e4a;
            --topbar-h:      56px;
            --subnav-bg:     #ffffff;
            --subnav-h:      44px;
            --primary:       #2563eb;
            --primary-dark:  #1d4ed8;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f1f5f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }

        /* ════════════════════════════════
           PRIMARY TOP BAR
        ════════════════════════════════ */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1050;
            height: var(--topbar-h);
            background: var(--topbar-bg);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }

        /* Brand */
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .3px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .topbar-brand i {
            font-size: 1.35rem;
            color: #60a5fa;
        }
        .topbar-brand:hover { color: #fff; }

        /* Spacer */
        .topbar-spacer { flex: 1; }

        /* User button */
        .topbar-user-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 8px;
            padding: 5px 12px 5px 6px;
            cursor: pointer;
            color: #fff;
            font-size: .85rem;
            font-weight: 500;
            transition: background .15s;
        }
        .topbar-user-btn:hover { background: rgba(255,255,255,.18); }

        .topbar-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        /* User dropdown */
        .topbar-dropdown {
            position: fixed;
            top: calc(var(--topbar-h) + 6px);
            right: 16px;
            min-width: 210px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.13);
            z-index: 9999;
            display: none;
            overflow: hidden;
        }
        .topbar-dropdown.open { display: block; }
        .topbar-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .topbar-dropdown-header strong {
            display: block;
            font-size: .88rem;
            color: #1e293b;
        }
        .topbar-dropdown-header small {
            color: #64748b;
            font-size: .78rem;
        }
        .topbar-dropdown a,
        .topbar-dropdown button {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            padding: 9px 16px;
            font-size: .86rem;
            color: #374151;
            text-decoration: none;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            transition: background .12s;
        }
        .topbar-dropdown a:hover,
        .topbar-dropdown button:hover { background: #f8fafc; color: #1e293b; }
        .topbar-dropdown .dropdown-logout { color: #dc2626; }
        .topbar-dropdown .dropdown-logout:hover { background: #fff5f5; }

        /* ════════════════════════════════
           SUB NAVIGATION
        ════════════════════════════════ */
        .subnav {
            position: sticky;
            top: var(--topbar-h);
            z-index: 1040;
            height: var(--subnav-h);
            background: var(--subnav-bg);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: stretch;
            padding: 0 16px;
            gap: 2px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .subnav::-webkit-scrollbar { display: none; }

        .subnav-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 0 14px;
            font-size: .875rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            transition: color .15s, border-color .15s;
            position: relative;
        }
        .subnav-item i {
            font-size: 1rem;
        }
        .subnav-item:hover {
            color: var(--primary);
        }
        .subnav-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }
        .subnav-item .badge-soon {
            font-size: .58rem;
            background: #e2e8f0;
            color: #94a3b8;
            padding: 2px 6px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* ════════════════════════════════
           BREADCRUMB BAR
        ════════════════════════════════ */
        .breadcrumb-bar {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            height: 34px;
            display: flex;
            align-items: center;
        }
        .breadcrumb {
            font-size: .8rem;
            margin: 0;
            padding: 0;
            background: none;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
        .breadcrumb-item a {
            color: #2563eb;
            text-decoration: none;
        }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item.active { color: #64748b; }
        .breadcrumb-item + .breadcrumb-item::before {
            color: #cbd5e1;
            content: "/";
        }

        /* ════════════════════════════════
           PAGE CONTENT
        ════════════════════════════════ */
        .page-content {
            padding: 24px;
        }

        /* ── Cards & Tables ── */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            border-radius: .5rem;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            border-radius: .5rem .5rem 0 0 !important;
        }
        .table th { font-weight: 600; color: #475569; }

        /* Badges */
        .badge-active   { background: #10b981; }
        .badge-inactive { background: #6b7280; }
        .badge-archived { background: #ef4444; }

        /* Buttons */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* ════════════════════════════════
           MOBILE
        ════════════════════════════════ */
        @media (max-width: 575.98px) {
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
            .topbar-brand span { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══════════════ PRIMARY TOP BAR ══════════════ --}}
<header class="topbar">
    <a class="topbar-brand" href="{{ route('patients.index') }}">
        <i class="bi bi-heart-pulse"></i>
        <span>{{ config('app.name') }}</span>
    </a>

    <div class="topbar-spacer"></div>

    {{-- User dropdown button --}}
    <button class="topbar-user-btn" onclick="toggleUserMenu()" id="userMenuBtn" type="button">
        <div class="topbar-avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <span>{{ auth()->user()->name ?? 'User' }}</span>
        <i class="bi bi-chevron-down" style="font-size:.7rem; opacity:.7;"></i>
    </button>
</header>

{{-- User dropdown panel --}}
<div class="topbar-dropdown" id="userDropdown">
    <div class="topbar-dropdown-header">
        <strong>{{ auth()->user()->name ?? 'User' }}</strong>
        <small>{{ auth()->user()->email ?? '' }}</small>
    </div>
    <a href="#">
        <i class="bi bi-person-circle"></i> User Profile
    </a>
    <a href="#">
        <i class="bi bi-gear"></i> Setting
    </a>
    <div style="border-top:1px solid #f1f5f9; margin:4px 0;"></div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="dropdown-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>

{{-- ══════════════ SUB NAVIGATION ══════════════ --}}
<nav class="subnav">
    <a href="{{ route('patients.index') }}"
       class="subnav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Patients
    </a>
    <a href="#"
       class="subnav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff"></i> Invoice
        <span class="badge-soon">Soon</span>
    </a>
    <a href="{{ route('drugstore.index') }}"
       class="subnav-item {{ request()->routeIs('drugstore.*') || request()->routeIs('drug-types.*') ? 'active' : '' }}">
        <i class="bi bi-capsule-pill"></i> Drugstore
    </a>
    <a href="#"
       class="subnav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line-fill"></i> Report
        <span class="badge-soon">Soon</span>
    </a>
    <a href="#"
       class="subnav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <i class="bi bi-gear-fill"></i> Setting
        <span class="badge-soon">Soon</span>
    </a>
</nav>

{{-- ══════════════ BREADCRUMB ══════════════ --}}
@hasSection('breadcrumb')
<div class="breadcrumb-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @yield('breadcrumb')
        </ol>
    </nav>
</div>
@endif

{{-- ══════════════ ALERTS ══════════════ --}}
<div style="padding: 16px 24px 0;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- ══════════════ PAGE CONTENT ══════════════ --}}
<main class="page-content">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('open');
    }

    document.addEventListener('click', function (e) {
        var btn      = document.getElementById('userMenuBtn');
        var dropdown = document.getElementById('userDropdown');
        if (dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
</script>
@stack('scripts')
</body>
</html>
