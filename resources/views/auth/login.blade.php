<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login &mdash; {{ config('app.name', 'Maika') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        html, body { height: 100%; margin: 0; }

        .auth-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 2rem 1rem;
        }

        .auth-wrap::before {
            content: '';
            position: fixed;
            top: -120px; right: -120px;
            width: 380px; height: 380px;
            background: rgba(99,102,241,.1);
            border-radius: 50%;
            pointer-events: none;
        }
        .auth-wrap::after {
            content: '';
            position: fixed;
            bottom: -100px; left: -100px;
            width: 320px; height: 320px;
            background: rgba(20,184,166,.09);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
            position: relative;
            z-index: 1;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.75rem;
        }
        .auth-brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #6366f1, #14b8a6);
            border-radius: .75rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(99,102,241,.3);
        }
        .auth-brand-icon i { font-size: 1.1rem; color: #fff; }
        .auth-brand-name {
            font-size: 1.2rem; font-weight: 700;
            color: #1e293b; line-height: 1.2;
        }
        .auth-brand-sub {
            font-size: .75rem; color: #94a3b8;
        }

        .auth-title {
            font-size: 1.4rem; font-weight: 700;
            color: #1e293b; margin-bottom: .35rem;
        }
        .auth-subtitle {
            font-size: .875rem; color: #64748b;
            margin-bottom: 1.75rem;
        }

        /* Override Bootstrap form-floating label */
        .form-floating > label { color: #94a3b8; font-size: .9rem; }
        .form-control {
            border-color: #e2e8f0;
            border-radius: .6rem !important;
            background: #f8fafc;
            font-size: .93rem;
        }
        .form-control:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        .btn-submit {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: .6rem;
            padding: .75rem;
            font-size: .95rem; font-weight: 600;
            letter-spacing: .01em;
            width: 100%;
            color: #fff;
            transition: opacity .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
        }
        .btn-submit:hover {
            opacity: .91; color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(99,102,241,.4);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .78rem;
            color: #94a3b8;
        }
        .auth-back {
            display: inline-flex; align-items: center; gap: .35rem;
            font-size: .82rem; color: #64748b;
            text-decoration: none; margin-bottom: 1.5rem;
        }
        .auth-back:hover { color: #6366f1; }
    </style>
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card">

            <a href="{{ url('/') }}" class="auth-back">
                <i class="fa-solid fa-arrow-left fa-xs"></i> Halaman Awal
            </a>

            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <div class="auth-brand-name">{{ config('app.name', 'Maika') }}</div>
                    <div class="auth-brand-sub">Sistem Manajemen Operasional</div>
                </div>
            </div>

            <div class="auth-title">Selamat datang</div>
            <div class="auth-subtitle">Masuk dengan akun Anda untuk melanjutkan</div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="font-size:.87rem; border-radius:.6rem;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="form-floating mb-3">
                    <input class="form-control @error('email') is-invalid @enderror"
                           id="inputEmail" type="email" name="email"
                           placeholder="email@contoh.com"
                           value="{{ old('email') }}" required autofocus>
                    <label for="inputEmail">Alamat Email</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input class="form-control @error('password') is-invalid @enderror"
                           id="inputPassword" type="password" name="password"
                           placeholder="Password" required>
                    <label for="inputPassword">Password</label>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" id="inputRemember" type="checkbox" name="remember">
                    <label class="form-check-label text-secondary" style="font-size:.875rem;" for="inputRemember">
                        Ingat Saya
                    </label>
                </div>

                <button class="btn btn-submit" type="submit">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
                </button>
            </form>

            <div class="auth-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'Maika') }}
            </div>

        </div>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>