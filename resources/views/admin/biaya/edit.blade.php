@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Biaya Harian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/biaya') }}">Biaya Harian</a></li>
            <li class="breadcrumb-item active">{{ $biaya->tanggal->format('Y-m-d') }}</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">Header</div>
            <div class="card-body">
                <form method="post" action="{{ url('/admin/biaya/'.$biaya->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $biaya->tanggal->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $biaya->catatan) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Tambah Detail</span>
                <span class="fw-semibold">Total: {{ number_format((float) $biaya->total_biaya, 2) }}</span>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('biaya.detail.store', $biaya) }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Nama Item</label>
                        <input type="text" name="nama_item" value="{{ old('nama_item') }}" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number" step="0.0001" min="0" name="qty" value="{{ old('qty') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" value="{{ old('satuan') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Harga Satuan</label>
                        <input type="number" step="0.01" min="0" name="harga_satuan" value="{{ old('harga_satuan') }}" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" type="submit">Tambah</button>
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
                                <th>Item</th>
                                <th class="text-end">Qty</th>
                                <th>Satuan</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($biaya->detail as $d)
                                <tr>
                                    <td class="fw-semibold">{{ $d->nama_item }}</td>
                                    <td class="text-end">{{ $d->qty !== null ? rtrim(rtrim(number_format((float) $d->qty, 4), '0'), '.') : '-' }}</td>
                                    <td>{{ $d->satuan }}</td>
                                    <td class="text-end">{{ $d->harga_satuan !== null ? number_format((float) $d->harga_satuan, 2) : '-' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format((float) $d->total, 2) }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('biaya.detail.edit', [$biaya, $d]) }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ route('biaya.detail.destroy', [$biaya, $d]) }}" onsubmit="return confirm('Hapus detail ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-body-secondary">Belum ada detail</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

