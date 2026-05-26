@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Ganti Password</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Profil · Ganti Password</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-key me-2"></i>Ubah Password Akun
            </div>
            <div class="card-body">
                <div class="mb-3 text-muted small">
                    Akun: <strong>{{ Auth::user()->email }}</strong>
                </div>

                <form method="POST" action="{{ url('/admin/password') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password"
                                       name="current_password"
                                       autocomplete="current-password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-pw="current_password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12"><hr class="mt-1 mb-0"></div>

                        <div class="col-md-4">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       autocomplete="new-password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-pw="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Minimal 8 karakter.</div>
                            <div class="mt-2 d-none" id="strength-wrap">
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar" id="strength-bar" role="progressbar" style="width:0%"></div>
                                </div>
                                <div class="form-text" id="strength-label"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       autocomplete="new-password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-pw="password_confirmation">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Password Baru
                            </button>
                            <a href="{{ url('/admin') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    // Toggle show/hide password
    document.querySelectorAll('[data-toggle-pw]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(this.dataset.togglePw);
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password strength indicator
    var pwInput = document.getElementById('password');
    var wrap = document.getElementById('strength-wrap');
    var bar = document.getElementById('strength-bar');
    var label = document.getElementById('strength-label');

    var levels = [
        { max: 20,  cls: 'bg-danger',  text: 'Sangat lemah' },
        { max: 40,  cls: 'bg-warning', text: 'Lemah' },
        { max: 60,  cls: 'bg-info',    text: 'Cukup' },
        { max: 80,  cls: 'bg-primary', text: 'Kuat' },
        { max: 100, cls: 'bg-success', text: 'Sangat kuat' },
    ];

    function calcStrength(pw) {
        var score = 0;
        if (pw.length >= 8)  score += 20;
        if (pw.length >= 12) score += 10;
        if (/[A-Z]/.test(pw)) score += 20;
        if (/[0-9]/.test(pw)) score += 20;
        if (/[^A-Za-z0-9]/.test(pw)) score += 30;
        return Math.min(score, 100);
    }

    pwInput.addEventListener('input', function () {
        var val = this.value;
        if (!val) { wrap.classList.add('d-none'); return; }
        wrap.classList.remove('d-none');
        var score = calcStrength(val);
        var lvl = levels.find(function (l) { return score <= l.max; }) || levels[levels.length - 1];
        bar.style.width = score + '%';
        bar.className = 'progress-bar ' + lvl.cls;
        label.textContent = lvl.text;
    });
})();
</script>
@endpush
