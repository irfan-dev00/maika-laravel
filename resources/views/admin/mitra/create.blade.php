@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/mitra') }}">Mitra</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/mitra') }}" class="row g-3">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label">Nama</label>
                        <input type="text" id="inp-nama" name="nama" value="{{ old('nama') }}" class="form-control" required autofocus>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            Kode Mitra
                            <span class="text-body-secondary small fw-normal">(3 huruf + 3 angka, mis. BAK002)</span>
                        </label>
                        <input type="text" id="inp-kode" name="kode_mitra"
                            value="{{ old('kode_mitra') }}"
                            class="form-control text-uppercase" maxlength="6"
                            pattern="[A-Z]{3}[0-9]{3}" required
                            placeholder="{{ 'XXX'.$nextNumber }}">
                        <div class="form-text">Diisi otomatis dari nama. Bisa diubah manual.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="is_aktif" {{ old('is_aktif', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_aktif">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
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
(function () {
    var nextNum = '{{ $nextNumber }}';
    var namaEl = document.getElementById('inp-nama');
    var kodeEl = document.getElementById('inp-kode');
    var userEdited = kodeEl.value.length > 0;

    namaEl.addEventListener('input', function () {
        if (userEdited) return;
        var letters = this.value.replace(/[^a-zA-Z]/g, '').substring(0, 3).toUpperCase();
        while (letters.length < 3) letters += 'X';
        kodeEl.value = letters + nextNum;
    });

    kodeEl.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
        userEdited = this.value.length > 0;
    });

    kodeEl.addEventListener('focus', function () {
        userEdited = this.value.length > 0;
    });
})();
</script>
@endpush

