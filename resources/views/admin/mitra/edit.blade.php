@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/mitra') }}">Mitra</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/mitra/'.$mitra->id) }}" class="row g-3">
                    @csrf
                    @method('put')
                    <div class="col-md-8">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama', $mitra->nama) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            Kode Mitra
                            <span class="text-body-secondary small fw-normal">(3 huruf + 3 angka)</span>
                        </label>
                        <input type="text" name="kode_mitra"
                            value="{{ old('kode_mitra', $mitra->kode_mitra) }}"
                            class="form-control text-uppercase" maxlength="6"
                            pattern="[A-Z]{3}[0-9]{3}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $mitra->telepon) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="is_aktif" {{ old('is_aktif', $mitra->is_aktif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_aktif">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $mitra->alamat) }}</textarea>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/mitra') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelector('input[name="kode_mitra"]').addEventListener('input', function () {
    var pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});
</script>
@endpush

