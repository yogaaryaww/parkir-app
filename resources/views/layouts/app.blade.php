<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aplikasi Parkir') - Sistem Parkir UKK</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #e2e8f0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar .brand {
            padding: 1.1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #38bdf8;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .sidebar-menu {
            padding: 0.75rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu .menu-header {
            padding: 0.65rem 1.5rem 0.25rem;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #475569;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.65rem 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            color: #ffffff;
            background-color: rgba(255,255,255,0.06);
            border-left-color: #38bdf8;
        }

        .sidebar-menu a i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
        }

        /* ===== CARD ===== */
        .card {
            border: none;
            border-radius: 0.65rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.25rem;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.85rem 1.25rem;
            font-weight: 600;
        }

        /* ===== STAT CARD ===== */
        .stat-card {
            border-left: 4px solid #3b82f6;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card.primary { border-left-color: #3b82f6; }
        .stat-card.success { border-left-color: #10b981; }
        .stat-card.warning { border-left-color: #f59e0b; }
        .stat-card.info    { border-left-color: #06b6d4; }

        /* ===== ALERT AUTO-DISMISS ===== */
        .alert { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    @stack('styles')
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">

        {{-- ============ SIDEBAR ============ --}}
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="brand">
                <i class="fa-solid fa-square-parking"></i>
                <span>PARKIR APP</span>
            </div>

            <ul class="sidebar-menu">
                @auth
                    {{-- ======== ADMIN MENU ======== --}}
                    @if(auth()->user()->role === 'admin')
                        <li class="menu-header">Utama</li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-gauge-high"></i> Dashboard
                            </a>
                        </li>

                        <li class="menu-header">Master Data</li>
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Kelola User
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.kategori.index') }}"
                                class="{{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-tags"></i> Kategori Kendaraan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.kendaraan.index') }}"
                                class="{{ request()->routeIs('admin.kendaraan.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-car"></i> Data Kendaraan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.tarif.index') }}"
                                class="{{ request()->routeIs('admin.tarif.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-money-bill-wave"></i> Tarif Parkir
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.area.index') }}"
                                class="{{ request()->routeIs('admin.area.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-layer-group"></i> Area Parkir
                            </a>
                        </li>

                        <li class="menu-header">Sistem</li>
                        <li>
                            <a href="{{ route('admin.log.index') }}"
                                class="{{ request()->routeIs('admin.log.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas
                            </a>
                        </li>

                    {{-- ======== PETUGAS MENU ======== --}}
                    @elseif(auth()->user()->role === 'petugas')
                        <li class="menu-header">Transaksi Parkir</li>
                        <li>
                            <a href="{{ route('petugas.transaksi.masuk') }}"
                                class="{{ request()->routeIs('petugas.transaksi.masuk') ? 'active' : '' }}">
                                <i class="fa-solid fa-right-to-bracket"></i> Kendaraan Masuk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('petugas.transaksi.keluar') }}"
                                class="{{ request()->routeIs('petugas.transaksi.keluar') ? 'active' : '' }}">
                                <i class="fa-solid fa-right-from-bracket"></i> Kendaraan Keluar
                            </a>
                        </li>

                    {{-- ======== OWNER MENU ======== --}}
                    @elseif(auth()->user()->role === 'owner')
                        <li class="menu-header">Laporan</li>
                        <li>
                            <a href="{{ route('owner.dashboard') }}"
                                class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-line"></i> Rekap Transaksi
                            </a>
                        </li>
                    @endif

                    {{-- LOGOUT selalu ada --}}
                    <li class="menu-header">Akun</li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="
                                background:none;border:none;width:100%;text-align:left;
                                display:flex;align-items:center;gap:0.8rem;
                                padding:0.65rem 1.5rem;color:#f87171;font-size:0.9rem;
                                font-weight:500;cursor:pointer;border-left:3px solid transparent;
                                transition:all 0.2s;
                            " onmouseover="this.style.backgroundColor='rgba(255,255,255,0.06)'"
                               onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fa-solid fa-power-off" style="width:18px;text-align:center;"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>

        {{-- ============ MAIN CONTENT ============ --}}
        <div class="col-md-9 col-lg-10 main-content">

            {{-- Navbar Top --}}
            <nav class="navbar-custom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark">@yield('title', 'Dashboard')</h6>
                @auth
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-semibold text-dark small">{{ auth()->user()->nama }}</div>
                        <span class="badge
                            @if(auth()->user()->role === 'admin') bg-danger
                            @elseif(auth()->user()->role === 'petugas') bg-primary
                            @else bg-success
                            @endif" style="font-size:0.7rem;">
                            {{ strtoupper(auth()->user()->role) }}
                        </span>
                    </div>
                </div>
                @endauth
            </nav>

            {{-- Content Area --}}
            <div class="container-fluid p-4">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            {{-- Footer --}}
            <footer class="mt-auto py-3 bg-white border-top text-center text-muted small">
                &copy; {{ date('Y') }} Aplikasi Parkir &mdash; Sistem Manajemen Parkir 
            </footer>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Auto dismiss alerts after 4s --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.querySelectorAll('.alert').forEach(function (el) {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                bsAlert.close();
            });
        }, 4000);
    });
</script>

@stack('scripts')
</body>
</html>
