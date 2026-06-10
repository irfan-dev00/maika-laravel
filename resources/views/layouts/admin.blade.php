<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Maika') }}</title>
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var theme = stored === 'light' || stored === 'dark'
                ? stored
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark" data-sb-topnav>
    <a class="navbar-brand ps-3" href="{{ url('/admin') }}">{{ config('app.name', 'Maika') }}</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="ms-auto d-flex align-items-center gap-2 pe-3">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-theme-toggle aria-pressed="false" title="Theme">
            <i class="fa-solid fa-moon"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-dark btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user me-1"></i><span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                    <a href="{{ route('password.form') }}" class="dropdown-item">
                        <i class="fa-solid fa-key me-2"></i>Ganti Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion" data-sb-sidenav>
            <div class="sb-sidenav-menu">
                <div class="nav">
                    {{-- CORE Section --}}
                    <button class="sb-section-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#coreSection" aria-expanded="true" aria-controls="coreSection">
                        <span>Core</span>
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div id="coreSection" class="collapse show" data-bs-parent="#sidenavAccordion">
                        <a class="nav-link {{ request()->is('admin') || request()->is('admin/operasi*') ? 'active' : '' }}" href="{{ url('/admin/operasi') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-gauge"></i></div>
                            Operasi Harian
                        </a>
                    </div>

                    {{-- MASTER DATA Section --}}
                    <button class="sb-section-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#masterDataSection" aria-expanded="true" aria-controls="masterDataSection">
                        <span>Master Data</span>
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div id="masterDataSection" class="collapse show" data-bs-parent="#sidenavAccordion">
                        <a class="nav-link {{ request()->is('admin/mitra*') ? 'active' : '' }}" href="{{ url('/admin/mitra') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-store"></i></div>
                            Mitra
                        </a>
                        <a class="nav-link {{ request()->is('admin/produk*') ? 'active' : '' }}" href="{{ url('/admin/produk') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-box"></i></div>
                            Produk
                        </a>
                        <a class="nav-link {{ request()->is('admin/harga*') ? 'active' : '' }}" href="{{ url('/admin/harga') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div>
                            Harga Bulanan
                        </a>
                        <a class="nav-link {{ request()->is('admin/kalender*') ? 'active' : '' }}" href="{{ url('/admin/kalender') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-calendar-days"></i></div>
                            Kalender Operasional
                        </a>
                    </div>

                    {{-- TRANSAKSI Section --}}
                    <button class="sb-section-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#transaksiSection" aria-expanded="true" aria-controls="transaksiSection">
                        <span>Transaksi</span>
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div id="transaksiSection" class="collapse show" data-bs-parent="#sidenavAccordion">
                        <a class="nav-link {{ request()->is('admin/produksi*') ? 'active' : '' }}" href="{{ url('/admin/produksi') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-industry"></i></div>
                            Produksi Harian
                        </a>
                        <a class="nav-link {{ request()->is('admin/pengiriman*') ? 'active' : '' }}" href="{{ url('/admin/pengiriman') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-truck"></i></div>
                            Pengiriman Mitra
                        </a>
                        <a class="nav-link {{ request()->is('admin/laporan*') ? 'active' : '' }}" href="{{ url('/admin/laporan') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                            Laporan Penjualan
                        </a>
                    </div>

                    {{-- KEUANGAN Section --}}
                    <button class="sb-section-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#keuanganSection" aria-expanded="true" aria-controls="keuanganSection">
                        <span>Keuangan</span>
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div id="keuanganSection" class="collapse show" data-bs-parent="#sidenavAccordion">
                        <a class="nav-link {{ request()->is('admin/biaya*') ? 'active' : '' }}" href="{{ url('/admin/biaya') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-receipt"></i></div>
                            Biaya Harian
                        </a>
                        <a class="nav-link {{ request()->is('admin/pembayaran*') ? 'active' : '' }}" href="{{ url('/admin/pembayaran') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                            Pembayaran Mitra
                        </a>
                    </div>

                    {{-- REKAP Section --}}
                    <button class="sb-section-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#rekapSection" aria-expanded="false" aria-controls="rekapSection">
                        <span>Rekap</span>
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div id="rekapSection" class="collapse" data-bs-parent="#sidenavAccordion">
                        <a class="nav-link {{ request()->is('admin/rekap') ? 'active' : '' }}" href="{{ url('/admin/rekap') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-gauge-high"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link {{ request()->is('admin/rekap/harian*') ? 'active' : '' }}" href="{{ url('/admin/rekap/harian') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-chart-line"></i></div>
                            Rekap Harian
                        </a>
                        <a class="nav-link {{ request()->is('admin/rekap/piutang*') ? 'active' : '' }}" href="{{ url('/admin/rekap/piutang') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                            Piutang Mitra
                        </a>
                        <a class="nav-link {{ request()->is('admin/rekap/produk*') ? 'active' : '' }}" href="{{ url('/admin/rekap/produk') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                            Produk Terlaris
                        </a>
                        <a class="nav-link {{ request()->is('admin/rekap/waste*') ? 'active' : '' }}" href="{{ url('/admin/rekap/waste') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-recycle"></i></div>
                            Waste & Kerugian
                        </a>
                        <a class="nav-link {{ request()->is('admin/rekap/laba-rugi*') ? 'active' : '' }}" href="{{ url('/admin/rekap/laba-rugi') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                            Laba Rugi Bulanan
                        </a>
                        <a class="nav-link {{ request()->is('admin/rekap/mitra*') ? 'active' : '' }}" href="{{ url('/admin/rekap/mitra') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-people-group"></i></div>
                            Performa Mitra
                        </a>
                    </div>

                    {{-- PENGATURAN Section (Owner only) --}}
                    @if(Auth::check() && Auth::user()->hasRole('Owner'))
                    <button class="sb-section-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#pengaturanSection" aria-expanded="false" aria-controls="pengaturanSection">
                        <span>Pengaturan</span>
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div id="pengaturanSection" class="collapse" data-bs-parent="#sidenavAccordion">
                        <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ url('/admin/users') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-users-gear"></i></div>
                            Manajemen User
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                {{ Auth::user()->name }}
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            @yield('content')
        </main>
        <footer class="py-4 bg-body-tertiary mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-body-secondary">{{ config('app.name', 'Maika') }}</div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
