@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Pembayaran Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/pembayaran') }}">Pembayaran Mitra</a></li>
            <li class="breadcrumb-item active">#{{ $pembayaran->id }}</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/pembayaran/'.$pembayaran->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $pembayaran->tanggal->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Mitra</label>
                        <select class="form-select" name="mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id', $pembayaran->mitra_id) == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Laporan Penjualan</label>
                        <select class="form-select" name="laporan_penjualan_mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($laporan as $l)
                                <option value="{{ $l->id }}" {{ old('laporan_penjualan_mitra_id', $pembayaran->laporan_penjualan_mitra_id) == $l->id ? 'selected' : '' }}>
                                    #{{ $l->id }} - {{ $l->tanggal->format('Y-m-d') }} - {{ optional($l->mitra)->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number" step="0.01" min="0" name="jumlah_bayar" value="{{ old('jumlah_bayar', $pembayaran->jumlah_bayar) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Metode</label>
                        <input type="text" name="metode" value="{{ old('metode', $pembayaran->metode) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="confirmed" {{ old('status', $pembayaran->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="draft" {{ old('status', $pembayaran->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $pembayaran->catatan) }}</textarea>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/pembayaran') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

