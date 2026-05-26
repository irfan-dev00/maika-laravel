@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Kalender Operasional</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/kalender') }}">Kalender Operasional</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/kalender/'.$kalender->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $kalender->tanggal->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="operasional" {{ old('status', $kalender->status) === 'operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="libur" {{ old('status', $kalender->status) === 'libur' ? 'selected' : '' }}>Libur</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan', $kalender->keterangan) }}" class="form-control">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/kalender') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

