@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Produksi Harian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/produksi') }}">Produksi Harian</a></li>
            <li class="breadcrumb-item active">{{ $produksi->tanggal->format('Y-m-d') }}</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">Header</div>
            <div class="card-body">
                <form method="post" action="{{ url('/admin/produksi/'.$produksi->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $produksi->tanggal->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $produksi->catatan) }}" class="form-control">
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
                <form method="post" action="{{ route('produksi.detail.store', $produksi) }}" class="row g-3">
                    @csrf
                    <div class="col-12 col-md-4">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="produk_id" required>
                            <option value="">Pilih</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" {{ old('produk_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Stok Awal <span class="text-body-secondary small">(kembali dari mitra)</span></label>
                        <input type="number" min="0" name="stok_awal" value="{{ old('stok_awal', 0) }}" class="form-control" required>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Jumlah Produksi <span class="text-body-secondary small">(baru hari ini)</span></label>
                        <input type="number" min="0" name="jumlah_produksi" value="{{ old('jumlah_produksi', 0) }}" class="form-control" required>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Layak Jual Kembali <span class="text-body-secondary small">(maks = stok awal)</span></label>
                        <input type="number" min="0" name="stok_layak_jual_kembali" value="{{ old('stok_layak_jual_kembali', 0) }}" class="form-control" required>
                    </div>
                    <div class="col-sm-6 col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" type="submit">Tambah</button>
                    </div>
                    <div class="col-12">
                        <div class="small text-body-secondary">Stok Siap Jual = Jumlah Produksi + Layak Jual Kembali</div>
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
                                <th class="text-end">Stok Awal <span class="fw-normal small text-body-secondary">(kembali dari mitra)</span></th>
                                <th class="text-end">Jumlah Produksi</th>
                                <th class="text-end">Layak Jual Kembali</th>
                                <th class="text-end text-danger">Tidak Layak <span class="fw-normal small">(terbuang)</span></th>
                                <th class="text-end">Stok Siap Jual <span class="fw-normal small text-body-secondary">(produksi + layak)</span></th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produksi->detail as $d)
                                @php $tidakLayak = max(0, $d->stok_awal - $d->stok_layak_jual_kembali); @endphp
                                <tr>
                                    <td>{{ optional($d->produk)->nama }}</td>
                                    <td class="text-end">{{ $d->stok_awal }}</td>
                                    <td class="text-end">{{ $d->jumlah_produksi }}</td>
                                    <td class="text-end">{{ $d->stok_layak_jual_kembali }}</td>
                                    <td class="text-end {{ $tidakLayak > 0 ? 'text-danger fw-semibold' : 'text-body-secondary' }}">
                                        {{ $tidakLayak > 0 ? $tidakLayak : '—' }}
                                    </td>
                                    <td class="text-end fw-semibold">{{ $d->stok_siap_jual }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('produksi.detail.edit', [$produksi, $d]) }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ route('produksi.detail.destroy', [$produksi, $d]) }}" onsubmit="return confirm('Hapus detail ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-body-secondary">Belum ada detail</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

