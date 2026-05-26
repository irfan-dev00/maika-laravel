@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Detail Pengiriman</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/pengiriman') }}">Pengiriman Mitra</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/admin/pengiriman/'.$pengiriman->id.'/edit') }}">#{{ $pengiriman->id }}</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('pengiriman.detail.update', [$pengiriman, $detail]) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-6">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="produk_id" disabled>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" {{ $detail->produk_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah Titip</label>
                        <input type="number" min="0" name="jumlah_titip" value="{{ old('jumlah_titip', $detail->jumlah_titip) }}" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Harga Jual</label>
                        <input type="text" class="form-control" value="{{ number_format((float) $detail->harga_jual, 2) }}" disabled>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Margin/Unit</label>
                        <input type="text" class="form-control" value="{{ $detail->margin_per_unit !== null ? number_format((float) $detail->margin_per_unit, 2) : '-' }}" disabled>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/pengiriman/'.$pengiriman->id.'/edit') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
