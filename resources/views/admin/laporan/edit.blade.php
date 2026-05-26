@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h1 class="mb-0">Edit Laporan Penjualan</h1>
            <a class="btn btn-success" href="{{ url('/admin/pembayaran/create?laporan_id='.$laporan->id) }}">
                Buat Pembayaran
            </a>
        </div>
        <ol class="breadcrumb mb-4 mt-2">
            <li class="breadcrumb-item"><a href="{{ url('/admin/laporan') }}">Laporan Penjualan</a></li>
            <li class="breadcrumb-item active">#{{ $laporan->id }}</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">Header</div>
            <div class="card-body">
                <form method="post" action="{{ url('/admin/laporan/'.$laporan->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $laporan->tanggal->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Mitra</label>
                        <select class="form-select" name="mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id', $laporan->mitra_id) == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $laporan->catatan) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Tambah Detail</div>
            <div class="card-body">
                <form method="post" action="{{ route('laporan.detail.store', $laporan) }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="produk_id" required>
                            <option value="">Pilih</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" {{ old('produk_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sisa Barang</label>
                        <input type="number" min="0" name="sisa_barang" value="{{ old('sisa_barang', 0) }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Tambah</button>
                        <div class="text-body-secondary small mt-2">Jumlah titip diambil dari Pengiriman Mitra, status layak jual kembali diambil dari Produksi Harian, dan harga jual serta margin diambil dari Harga Bulanan pada tanggal laporan yang sama.</div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Detail</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Titip</th>
                                <th class="text-end">Sisa</th>
                                <th class="text-end">Layak</th>
                                <th class="text-end">Terjual</th>
                                <th class="text-end">Tidak Layak</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan->detail as $d)
                                <tr>
                                    <td>{{ optional($d->produk)->nama }}</td>
                                    <td class="text-end">{{ $d->jumlah_titip }}</td>
                                    <td class="text-end">{{ $d->sisa_barang }}</td>
                                    <td class="text-end">{{ $d->stok_layak_jual_kembali }}</td>
                                    <td class="text-end fw-semibold">{{ $d->jumlah_terjual }}</td>
                                    <td class="text-end">{{ $d->stok_tidak_layak_jual }}</td>
                                    <td class="text-end">{{ number_format((float) $d->total_penjualan, 2) }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('laporan.detail.edit', [$laporan, $d]) }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ route('laporan.detail.destroy', [$laporan, $d]) }}" onsubmit="return confirm('Hapus detail ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-body-secondary">Belum ada detail</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
