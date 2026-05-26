@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Detail Biaya</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/biaya') }}">Biaya Harian</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/biaya/'.$biaya->id.'/edit') }}">{{ $biaya->tanggal->format('Y-m-d') }}</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('biaya.detail.update', [$biaya, $detail]) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Nama Item</label>
                        <input type="text" name="nama_item" value="{{ old('nama_item', $detail->nama_item) }}" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" step="0.0001" min="0" name="qty" value="{{ old('qty', $detail->qty) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" value="{{ old('satuan', $detail->satuan) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Harga Satuan</label>
                        <input type="number" step="0.01" min="0" name="harga_satuan" value="{{ old('harga_satuan', $detail->harga_satuan) }}" class="form-control">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/biaya/'.$biaya->id.'/edit') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

