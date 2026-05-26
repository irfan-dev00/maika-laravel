@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Pengiriman Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/pengiriman') }}">Pengiriman Mitra</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/pengiriman') }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Mitra</label>
                        <select class="form-select" name="mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}" class="form-control">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/pengiriman') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

