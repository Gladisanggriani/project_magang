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
    @stack('scripts')

    <style>
        /* CSS Untuk Fixed Footer */
        .dashboard-body {
            /* Menambahkan padding bawah agar konten tidak tertutup footer */
            padding-bottom: 70px;
            /* Pastikan body setidaknya setinggi layar agar footer selalu di bawah
               walau konten sedikit */
            min-height: 100vh;
            position: relative;
        }

        .fixed-footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #ffffff; /* Bisa diubah sesuai tema */
            color: #4b5563;
            padding: 16px 24px;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
            border-top: 1px solid #e5e7eb;
            z-index: 9999;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Responsif untuk layar kecil (HP) */
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
            .dashboard-body {
                padding-bottom: 90px; /* Padding lebih besar di HP karena footer menumpuk */
            }
        }
    </style>
</head>

<body class="dashboard-body">

    <div class="app-shell">
        <header class="app-navbar">
            <div class="brand-block">
                <div class="brand-logo-img">
                    <img src="{{ asset('images/logo_sp.png') }}" alt="Logo PT Semen Padang">
                </div>

                <div>
                    <div class="brand-title">Dashboard Operasional Unit Pabrik Dumai</div>

                    <div class="brand-subtitle">
                        PT Semen Padang - Monitoring Produksi dan Operasional Harian

                        @auth
                            • Role: {{ strtoupper(auth()->user()->role) }}
                        @else
                            • Mode: VIEWER
                        @endauth

                    </div>
                </div>
            </div>

            <div class="nav-links">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.public') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.show') || request()->routeIs('reports.preview-monthly') ? 'active' : '' }}">
                    Riwayat Laporan
                </a>

                @auth
                    @if (auth()->user()->hasRole(['admin', 'operator']))
                        <a href="{{ route('reports.create') }}"
                            class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}">
                            Input Laporan
                        </a>
                    @endif
                @endauth

                <a href="{{ route('rakps.index') }}"
                    class="nav-link {{ request()->routeIs('rakps.*') ? 'active' : '' }}">
                    RKAP
                </a>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login Admin/Operator
                    </a>
                @endauth
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

    <footer class="fixed-footer">
        <div class="footer-content">
            <span>&copy; {{ date('Y') }} Operasional Unit Pabrik Dumai. All rights reserved.</span>
            <span>PT Semen Padang - Versi 1.0</span>
        </div>
    </footer>

</body>

</html>
