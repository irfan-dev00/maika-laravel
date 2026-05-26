@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Laporan Penjualan Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Transaksi</li>
        </ol>

        @include('admin.partials.flash')

        @include('admin.partials.date-navigator', [
            'navBaseUrl'    => url('/admin/laporan'),
            'navTanggal'    => $tanggal,
            'navTanggalStr' => $tanggalStr,
            'navExtraParams' => ['mitra_id' => $mitraId],
        ])

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/laporan') }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggalStr }}">
                    <div class="col-12 col-md-5">
                        <label class="form-label mb-0">Mitra</label>
                        <select class="form-select" name="mitra_id">
                            <option value="">Semua</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ (string) $mitraId === (string) $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-7 d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" type="submit">Filter Mitra</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/laporan?tanggal='.$tanggalStr) }}">Reset</a>
                        <a class="btn btn-outline-info ms-auto" href="{{ url('/admin/laporan/matriks?tanggal='.$tanggalStr) }}">Matriks</a>
                        <a class="btn btn-primary" href="{{ url('/admin/laporan/create') }}">Tambah</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mitra</th>
                                <th>Jumlah Produk</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $row)
                                <tr>
                                    <td class="text-nowrap">{{ $row->tanggal->format('Y-m-d') }}</td>
                                    <td>{{ optional($row->mitra)->nama }}</td>
                                    <td>{{ $row->detail_count }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ url('/admin/laporan/'.$row->id.'/edit') }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ url('/admin/laporan/'.$row->id) }}" onsubmit="return confirm('Hapus laporan ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-body-secondary">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $laporan->links() }}
            </div>
        </div>
    </div>
@endsection

