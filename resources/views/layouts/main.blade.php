<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard Operasional Dumai')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('images/logo_sp.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="dashboard-body">

    <div class="app-shell">
        <header class="app-navbar">
            <div class="brand-block">
                <div class="brand-logo-img">
                    <img src="{{ asset('images/logo_sp.png') }}" alt="Logo PT Semen Padang">
                </div>
                <div>
                    <div class="brand-title">Dashboard Operasional GP Dumai</div>
                    <div class="brand-subtitle">
                        PT Semen Padang - Monitoring Produksi dan Operasional Harian
                        • Role: {{ strtoupper(auth()->user()->role) }}
                        @if (request()->routeIs('dashboard'))
                            • Auto refresh 30 detik
                        @endif
                    </div>
                </div>
            </div>

            <div class="nav-links">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    Riwayat
                </a>

                @if (auth()->user()->hasRole(['admin', 'operator']))
                    <a href="{{ route('reports.create') }}"
                        class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}">
                        Input Laporan
                    </a>
                @endif

                {{-- @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        Manajemen User
                    </a>
                @endif --}}

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </header>

        <main class="page-content">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

</body>

</html>
