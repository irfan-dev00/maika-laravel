@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Harga Bulanan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/harga') }}">Harga Bulanan</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/harga/'.$harga->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <input type="hidden" name="unique_check" value="1">
                    <div class="col-md-6">
                        <label class="form-label">Mitra</label>
                        <select class="form-select" name="mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id', $harga->mitra_id) == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="produk_id" required>
                            <option value="">Pilih</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" {{ old('produk_id', $harga->produk_id) == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" value="{{ old('tahun', $harga->tahun) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <input type="number" name="bulan" value="{{ old('bulan', $harga->bulan) }}" min="1" max="12" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" step="0.01" min="0" name="harga_jual" value="{{ old('harga_jual', $harga->harga_jual) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Margin/Unit</label>
                        <input type="number" step="0.01" min="0" name="margin_per_unit" value="{{ old('margin_per_unit', $harga->margin_per_unit) }}" class="form-control">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/harga') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

