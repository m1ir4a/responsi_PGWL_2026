<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PGWL' }} — WebGIS Pertanian Kab. Grobogan</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">



    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --pgwl-green:       #1a7d3b;
            --pgwl-green-light: #28a745;
            --pgwl-green-pale:  #e8f5e9;
            --pgwl-gold:        #f59e0b;
            --pgwl-dark:        #0d1f14;
            --pgwl-surface:     #f0f7f2;
            --pgwl-text:        #1c2b22;
            --pgwl-muted:       #6b7c70;
            --pgwl-border:      #c8dece;
            --navbar-h:         60px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--pgwl-surface);
            color: var(--pgwl-text);
            margin: 0;
            padding: 0;
        }

        /* ── NAVBAR ── */
        .pgwl-navbar {
            height: var(--navbar-h);
            background: var(--pgwl-dark);
            border-bottom: 3px solid var(--pgwl-green);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            position: sticky;
            top: 0;
            z-index: 9999;
            gap: 1rem;
        }

        .pgwl-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
            letter-spacing: -.01em;
            white-space: nowrap;
        }

        .pgwl-brand .brand-icon {
            width: 32px; height: 32px;
            background: var(--pgwl-green);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
        }

        .pgwl-nav {
            display: flex;
            align-items: center;
            gap: .25rem;
            flex: 1;
        }

        .pgwl-nav a {
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            padding: .35rem .75rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: .4rem;
            transition: all .15s;
        }

        .pgwl-nav a:hover,
        .pgwl-nav a.active {
            color: #fff;
            background: rgba(255,255,255,.1);
        }

        .pgwl-nav a.active {
            background: var(--pgwl-green);
        }

        .pgwl-nav-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .pgwl-user-badge {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: rgba(255,255,255,.85);
            font-size: .82rem;
        }

        .pgwl-avatar {
            width: 30px; height: 30px;
            background: var(--pgwl-green);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
        }

        .btn-logout {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            color: rgba(255,255,255,.8);
            font-size: .78rem;
            padding: .3rem .65rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all .15s;
        }

        .btn-logout:hover {
            background: rgba(220,38,38,.7);
            color: #fff;
            border-color: transparent;
        }

        /* ── PAGE CONTENT ── */
        .pgwl-page {
            min-height: calc(100vh - var(--navbar-h));
        }

        /* ── CARD ── */
        .pgwl-card {
            background: #fff;
            border: 1px solid var(--pgwl-border);
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        /* ── ALERT TOAST ── */
        .pgwl-alert {
            position: fixed;
            top: calc(var(--navbar-h) + 12px);
            right: 16px;
            z-index: 10000;
            min-width: 280px;
            max-width: 400px;
        }
    </style>

    @yield('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="pgwl-navbar">
    <a class="pgwl-brand" href="{{ route('home') }}">
        <span class="brand-icon"><i class="fa-solid fa-seedling"></i></span>
        PGWL Grobogan
    </a>

    <div class="pgwl-nav">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Beranda
        </a>
        <a href="{{ route('peta') }}" class="{{ request()->routeIs('peta') ? 'active' : '' }}">
            <i class="fa-solid fa-map-location-dot"></i> Peta
        </a>
        <a href="{{ route('tabel') }}" class="{{ request()->routeIs('tabel') ? 'active' : '' }}">
            <i class="fa-solid fa-table-list"></i> Tabel Data
        </a>
    </div>

    <div class="pgwl-nav-right">
        @auth
            <div class="pgwl-user-badge">
                <div class="pgwl-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-logout" style="border-color:var(--pgwl-green);color:#fff">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </a>
        @endauth
    </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success'))
    <div class="pgwl-alert" id="flashAlert">
        <div class="alert alert-success alert-dismissible shadow d-flex align-items-center gap-2 mb-0" role="alert">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="pgwl-alert" id="flashAlert">
        <div class="alert alert-danger alert-dismissible shadow d-flex align-items-center gap-2 mb-0" role="alert">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

{{-- CONTENT --}}
<div class="pgwl-page">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide flash after 4s
    setTimeout(function(){
        var el = document.getElementById('flashAlert');
        if(el) el.style.transition = 'opacity .5s', el.style.opacity = '0', setTimeout(()=>el.remove(), 500);
    }, 4000);

    // Mark active nav
    document.querySelectorAll('.pgwl-nav a').forEach(a => {
        if(a.href === window.location.href) a.classList.add('active');
    });
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>
