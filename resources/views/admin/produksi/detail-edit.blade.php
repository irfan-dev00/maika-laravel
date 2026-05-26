@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Detail Produksi</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/produksi') }}">Produksi Harian</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/produksi/'.$produksi->id.'/edit') }}">{{ $produksi->tanggal->format('Y-m-d') }}</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('produksi.detail.update', [$produksi, $detail]) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-12 col-md-6">
                        <label class="form-label">Produk</label>
                        <input type="text" class="form-control" value="{{ optional($detail->produk)->nama }}" disabled>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" min="0" name="stok_awal" value="{{ old('stok_awal', $detail->stok_awal) }}" class="form-control" required>
                        <div class="form-text">Total barang kembali dari mitra (tidak terjual).</div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Jumlah Produksi</label>
                        <input type="number" min="0" name="jumlah_produksi" value="{{ old('jumlah_produksi', $detail->jumlah_produksi) }}" class="form-control" required>
                        <div class="form-text">Produk baru dibuat hari ini.</div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Layak Jual Kembali</label>
                        <input type="number" min="0" name="stok_layak_jual_kembali" value="{{ old('stok_layak_jual_kembali', $detail->stok_layak_jual_kembali) }}" class="form-control" required>
                        <div class="form-text">Dari stok awal yg lolos cek (maks {{ $detail->stok_awal }}).</div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label">Stok Siap Jual</label>
                        <input type="text" class="form-control bg-light" value="{{ $detail->stok_siap_jual }}" disabled>
                        <div class="form-text">Produksi + Layak Jual Kembali.</div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/produksi/'.$produksi->id.'/edit') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

