@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Produk</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Master Data</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <form class="d-flex gap-2" method="get" action="{{ url('/admin/produk') }}">
                        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari kode/nama">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/produk') }}">Reset</a>
                    </form>
                    <a class="btn btn-primary" href="{{ url('/admin/produk/create') }}">Tambah</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Satuan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produk as $row)
                                <tr>
                                    <td class="text-nowrap">{{ $row->kode_produk }}</td>
                                    <td class="fw-semibold">{{ $row->nama }}</td>
                                    <td>{{ $row->satuan }}</td>
                                    <td>
                                        @if ($row->is_aktif)
                                            <span class="badge text-bg-success">Aktif</span>
                                        @else
                                            <span class="badge text-bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ url('/admin/produk/'.$row->id.'/edit') }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ url('/admin/produk/'.$row->id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-body-secondary">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $produk->links() }}
            </div>
        </div>
    </div>
@endsection

