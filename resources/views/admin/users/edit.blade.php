@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit User</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>

        @include('admin.partials.flash')

        {{-- Form Edit Data --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Data User</div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">— Pilih Role —</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}"
                                        {{ old('role_id', optional($currentRole)->id) == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12"><hr class="mt-1 mb-0"><div class="form-text text-muted">Kosongkan field password jika tidak ingin mengubah password.</div></div>

                        <div class="col-md-5">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" data-toggle-pw="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mt-2 d-none" id="strength-wrap">
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar" id="strength-bar" role="progressbar" style="width:0%"></div>
                                </div>
                                <div class="form-text" id="strength-label"></div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control"
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" data-toggle-pw="password_confirmation">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Reset Password Cepat --}}
        @if ($user->id !== auth()->id())
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="fa-solid fa-rotate me-2"></i>Reset Password
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Reset password user <strong>{{ $user->name }}</strong> ke password baru tanpa perlu mengetahui password lama.</p>
                    <form method="POST" action="{{ route('users.reset-password', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label mb-0">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password"
                                           id="new_password"
                                           name="new_password"
                                           class="form-control @error('new_password') is-invalid @enderror"
                                           autocomplete="new-password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-pw="new_password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label mb-0">Konfirmasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password"
                                           id="new_password_confirmation"
                                           name="new_password_confirmation"
                                           class="form-control"
                                           autocomplete="new-password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-pw="new_password_confirmation">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-warning w-100">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
@include('admin.partials.pw-toggle-script')
@endpush
