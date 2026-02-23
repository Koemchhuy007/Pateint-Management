<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --topbar-bg:    #1a2e4a;
            --primary:      #2563eb;
            --primary-dark: #1d4ed8;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── Left panel ── */
        .login-left {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            background: #ffffff;
            overflow-y: auto;
        }

        @media (min-width: 768px) {
            .login-left { width: 50%; }
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        /* Mobile brand header */
        .login-brand-mobile {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }
        .login-brand-mobile .brand-icon {
            width: 40px; height: 40px;
            background: var(--topbar-bg);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #60a5fa;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .login-brand-mobile h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--topbar-bg);
            margin: 0;
        }

        .login-heading h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .35rem;
        }
        .login-heading p {
            color: #64748b;
            font-size: .9rem;
            margin-bottom: 0;
        }

        /* Form controls */
        .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: .4rem;
        }
        .form-label i { font-size: .9rem; color: #94a3b8; }

        .form-control {
            height: 52px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: .925rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: none;
        }

        /* Submit button */
        .btn-login {
            height: 52px;
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 600;
            letter-spacing: .015em;
            transition: background .15s, box-shadow .15s, transform .1s;
            box-shadow: 0 4px 12px rgba(37,99,235,.25);
        }
        .btn-login:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: 0 6px 16px rgba(37,99,235,.35);
        }
        .btn-login:active { transform: scale(.98); }

        /* Remember me */
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .form-check-label { font-size: .85rem; color: #475569; }

        /* ── Right branding panel ── */
        .login-right {
            display: none;
            width: 50%;
            background: var(--topbar-bg);
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .login-right { display: flex; }
        }

        /* Dot pattern overlay */
        .login-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,.08) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        /* Glow blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .blob-1 {
            width: 320px; height: 320px;
            background: rgba(37,99,235,.35);
            bottom: -80px; right: -80px;
        }
        .blob-2 {
            width: 280px; height: 280px;
            background: rgba(96,165,250,.2);
            top: -60px; left: -60px;
        }

        .branding-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 3rem;
            color: #fff;
        }

        .branding-icon-wrap {
            width: 88px; height: 88px;
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
            box-shadow: 0 8px 32px rgba(0,0,0,.25);
        }
        .branding-icon-wrap i {
            font-size: 2.5rem;
            color: #60a5fa;
        }

        .branding-content h1 {
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -.01em;
            margin-bottom: .75rem;
            line-height: 1.2;
        }

        .branding-content p {
            font-size: 1rem;
            color: rgba(255,255,255,.65);
            max-width: 300px;
            line-height: 1.6;
            margin: 0;
        }

        /* Feature pills */
        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-top: 2rem;
        }
        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            padding: 5px 12px;
            font-size: .78rem;
            font-weight: 500;
            color: rgba(255,255,255,.8);
        }
        .feature-pill i { font-size: .8rem; color: #60a5fa; }
    </style>
</head>
<body>

    {{-- ── Left: Login Form ── --}}
    <div class="login-left">
        <div class="login-box">

            {{-- Mobile brand (hidden on md+) --}}
            <div class="login-brand-mobile d-md-none">
                <div class="brand-icon">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <h1>{{ config('app.name') }}</h1>
            </div>

            {{-- Heading --}}
            <div class="login-heading mb-4">
                <h2>Welcome back</h2>
                <p>Please enter your credentials to access your secure medical portal.</p>
            </div>

            {{-- Error alert --}}
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-4" style="font-size:.875rem; border-radius:8px;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Username --}}
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-at"></i> Username
                    </label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username') }}"
                        placeholder="e.g. j.doe"
                        required
                        autofocus
                    >
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                    >
                </div>

                {{-- Remember me --}}
                <div class="mb-4 form-check ps-0 d-flex align-items-center gap-2" style="padding-left:0!important;">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="form-check-input mt-0"
                        style="width:1rem; height:1rem; cursor:pointer;"
                    >
                    <label for="remember" class="form-check-label" style="cursor:pointer;">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary btn-login w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>

        </div>
    </div>

    {{-- ── Right: Branding Panel ── --}}
    <div class="login-right">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <div class="branding-content">
            <div class="branding-icon-wrap">
                <i class="bi bi-heart-pulse"></i>
            </div>
            <h1>{{ config('app.name') }}</h1>
            <p>The next generation of secure patient management systems.</p>

            <div class="feature-pills mt-4">
                <span class="feature-pill"><i class="bi bi-people-fill"></i> Patient Records</span>
                <span class="feature-pill"><i class="bi bi-capsule-pill"></i> Drugstore</span>
                <span class="feature-pill"><i class="bi bi-bar-chart-line-fill"></i> Reports</span>
                <span class="feature-pill"><i class="bi bi-shield-check"></i> Secure Access</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
