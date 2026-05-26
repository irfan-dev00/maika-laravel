@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Pengiriman Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/pengiriman') }}">Pengiriman Mitra</a></li>
            <li class="breadcrumb-item active">#{{ $pengiriman->id }}</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">Header</div>
            <div class="card-body">
                <form method="post" action="{{ url('/admin/pengiriman/'.$pengiriman->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $pengiriman->tanggal->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Mitra</label>
                        <select class="form-select" name="mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id', $pengiriman->mitra_id) == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $pengiriman->catatan) }}" class="form-control">
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
                @if ($produkTanpaHarga->count() > 0)
                    <div class="alert alert-warning py-2 small">
                        Produk berikut <strong>belum punya harga bulanan</strong> untuk mitra ini di periode {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}:
                        <strong>{{ $produkTanpaHarga->pluck('nama')->implode(', ') }}</strong>.
                        <a href="{{ url('/admin/harga-produk-mitra-bulanan/create') }}" class="alert-link">Set harga</a>
                    </div>
                @endif
                <form method="post" action="{{ route('pengiriman.detail.store', $pengiriman) }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="produk_id" required {{ $produk->count() === 0 ? 'disabled' : '' }}>
                            <option value="">{{ $produk->count() === 0 ? 'Tidak ada produk tersisa / berharga' : 'Pilih' }}</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" {{ old('produk_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah Titip</label>
                        <input type="number" min="0" name="jumlah_titip" value="{{ old('jumlah_titip', 0) }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit" {{ $produk->count() === 0 ? 'disabled' : '' }}>Tambah</button>
                        <div class="text-body-secondary small mt-2">Hanya produk dengan harga bulanan & belum ada di detail yang ditampilkan.</div>
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
                                <th class="text-end">Jumlah Titip</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Margin/Unit</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengiriman->detail as $d)
                                <tr>
                                    <td>{{ optional($d->produk)->nama }}</td>
                                    <td class="text-end">{{ $d->jumlah_titip }}</td>
                                    <td class="text-end">{{ number_format((float) $d->harga_jual, 2) }}</td>
                                    <td class="text-end">{{ $d->margin_per_unit !== null ? number_format((float) $d->margin_per_unit, 2) : '-' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('pengiriman.detail.edit', [$pengiriman, $d]) }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ route('pengiriman.detail.destroy', [$pengiriman, $d]) }}" onsubmit="return confirm('Hapus detail ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-body-secondary">Belum ada detail</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
