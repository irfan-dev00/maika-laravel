@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Produk</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/produk') }}">Produk</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/produk/'.$produk->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" name="kode_produk" value="{{ old('kode_produk', $produk->kode_produk) }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama', $produk->nama) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($satuan as $key => $label)
                                <option value="{{ $key }}" {{ old('satuan', $produk->satuan) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Modal/Unit</label>
                        <input type="number" step="0.01" min="0" name="harga_modal_per_unit" value="{{ old('harga_modal_per_unit', $produk->harga_modal_per_unit) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="is_aktif" {{ old('is_aktif', $produk->is_aktif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_aktif">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/produk') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

