<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Maika') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        html, body { height: 100%; margin: 0; }

        .welcome-wrap {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 2rem 1rem;
        }

        /* Dekorasi blob latar */
        .welcome-wrap::before {
            content: '';
            position: fixed;
            top: -120px; right: -120px;
            width: 380px; height: 380px;
            background: rgba(99,102,241,.12);
            border-radius: 50%;
            pointer-events: none;
        }
        .welcome-wrap::after {
            content: '';
            position: fixed;
            bottom: -100px; left: -100px;
            width: 320px; height: 320px;
            background: rgba(20,184,166,.1);
            border-radius: 50%;
            pointer-events: none;
        }

        .welcome-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 2.75rem 2.5rem;
            max-width: 460px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 68px; height: 68px;
            background: linear-gradient(135deg, #6366f1, #14b8a6);
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
        }
        .brand-icon i { font-size: 1.8rem; color: #fff; }

        .brand-name {
            font-size: 1.9rem; font-weight: 700;
            color: #1e293b; letter-spacing: .02em;
            margin-bottom: .3rem;
        }

        .brand-tagline {
            font-size: .92rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 0 0 1.75rem;
        }

        .feature-list {
            list-style: none; padding: 0; margin: 0 0 2.25rem;
            text-align: left;
        }
        .feature-list li {
            display: flex; align-items: center; gap: .7rem;
            color: #475569;
            font-size: .875rem; padding: .4rem 0;
        }
        .feature-list li .fi {
            width: 32px; height: 32px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-list li .fi i { font-size: .82rem; color: #6366f1; }

        .btn-login {
            display: inline-flex; align-items: center; gap: .55rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border: none; border-radius: .75rem;
            padding: .8rem 2.25rem;
            font-size: .97rem; font-weight: 600;
            text-decoration: none;
            width: 100%; justify-content: center;
            transition: opacity .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
        }
        .btn-login:hover {
            opacity: .92; color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(99,102,241,.45);
        }

        .footer-note {
            margin-top: 1.5rem;
            font-size: .78rem;
            color: #94a3b8;
            position: relative; z-index: 1;
        }

        /* --- Auth state: sudah login --- */
        .user-avatar {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #6366f1, #14b8a6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.6rem; color: #fff; font-weight: 700;
            box-shadow: 0 4px 14px rgba(99,102,241,.3);
        }
        .user-name {
            font-size: 1.35rem; font-weight: 700;
            color: #1e293b; margin-bottom: .2rem;
        }
        .user-role {
            display: inline-block;
            background: #eef2ff; color: #6366f1;
            font-size: .75rem; font-weight: 600;
            padding: .2rem .7rem;
            border-radius: 999px;
            margin-bottom: 1.6rem;
        }
        .user-info-row {
            display: flex; align-items: center; gap: .6rem;
            color: #64748b; font-size: .875rem;
            padding: .45rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .user-info-row:last-of-type { border-bottom: none; }
        .user-info-row i { color: #94a3b8; width: 16px; text-align: center; }
        .user-info-block { margin-bottom: 2rem; }
        .btn-dashboard {
            display: inline-flex; align-items: center; gap: .55rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border: none; border-radius: .75rem;
            padding: .8rem 2.25rem;
            font-size: .97rem; font-weight: 600;
            text-decoration: none;
            width: 100%; justify-content: center;
            transition: opacity .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
            margin-bottom: .75rem;
        }
        .btn-dashboard:hover {
            opacity: .91; color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(99,102,241,.4);
        }
        .btn-logout-link {
            display: block; text-align: center;
            font-size: .83rem; color: #94a3b8;
            cursor: pointer; background: none; border: none; width: 100%;
            padding: .25rem 0;
        }
        .btn-logout-link:hover { color: #ef4444; }
    </style>
</head>
<body>
    <div class="welcome-wrap">
        <div class="welcome-card">

            <div class="brand-icon">
                <i class="fa-solid fa-store"></i>
            </div>

            <div class="brand-name">{{ config('app.name', 'Maika') }}</div>
            <div class="brand-tagline">Sistem Manajemen Operasional</div>

            <div class="divider"></div>

            @auth
                {{-- Sudah login: tampilkan data diri --}}
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>

                <div class="user-info-block">
                    <div class="user-info-row">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ Auth::user()->email }}</span>
                    </div>
                    <div class="user-info-row">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Bergabung {{ Auth::user()->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <a href="{{ url('/admin') }}" class="btn-dashboard">
                    <i class="fa-solid fa-gauge"></i>
                    Masuk ke Dashboard
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout-link">
                        <i class="fa-solid fa-right-from-bracket fa-xs me-1"></i> Keluar
                    </button>
                </form>

            @else
                {{-- Belum login: tampilkan fitur + tombol login --}}
                <ul class="feature-list">
                    <li>
                        <span class="fi"><i class="fa-solid fa-industry"></i></span>
                        <span>Kelola produksi &amp; pengiriman harian</span>
                    </li>
                    <li>
                        <span class="fi"><i class="fa-solid fa-users"></i></span>
                        <span>Monitor kinerja mitra bisnis</span>
                    </li>
                    <li>
                        <span class="fi"><i class="fa-solid fa-receipt"></i></span>
                        <span>Catat biaya &amp; pembayaran</span>
                    </li>
                    <li>
                        <span class="fi"><i class="fa-solid fa-chart-line"></i></span>
                        <span>Rekap &amp; laporan keuangan</span>
                    </li>
                </ul>

                <a href="{{ url('/login') }}" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk ke Sistem
                </a>
            @endauth

        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} {{ config('app.name', 'Maika') }}
        </div>
    </div>
</body>
</html>
