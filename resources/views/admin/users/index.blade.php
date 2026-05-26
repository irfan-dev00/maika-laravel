@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Manajemen User</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Pengaturan · User</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-users-gear me-2"></i>Daftar User</span>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i>Tambah User
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $i => $u)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $u->name }}</div>
                                    @if ($u->id === auth()->id())
                                        <span class="badge bg-info text-dark small">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @foreach ($u->roles as $r)
                                        <span class="badge {{ $r->nama === 'Owner' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $r->nama }}
                                        </span>
                                    @endforeach
                                    @if ($u->roles->isEmpty())
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $u->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if ($u->id !== auth()->id())
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger ms-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-hapus-{{ $u->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            {{-- Modal Konfirmasi Hapus --}}
                            @if ($u->id !== auth()->id())
                                <div class="modal fade" id="modal-hapus-{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Yakin hapus user <strong>{{ $u->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form method="POST" action="{{ route('users.destroy', $u) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
