@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Harga Bulanan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/harga') }}">Harga Bulanan</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ url('/admin/harga') }}" class="row g-3">
                    @csrf
                    <input type="hidden" name="unique_check" value="1">
                    <div class="col-md-6">
                        <label class="form-label">Mitra</label>
                        <select class="form-select" id="sel-mitra" name="mitra_id" required>
                            <option value="">Pilih</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Produk</label>
                        <select class="form-select" id="sel-produk" name="produk_id" required>
                            <option value="">Pilih</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}" data-nama="{{ $p->nama }}"
                                    {{ old('produk_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" id="inp-tahun" name="tahun" value="{{ old('tahun', date('Y')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <input type="number" id="inp-bulan" name="bulan" value="{{ old('bulan', date('n')) }}" min="1" max="12" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" step="0.01" min="0" name="harga_jual" value="{{ old('harga_jual') }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Margin/Unit</label>
                        <input type="number" step="0.01" min="0" name="margin_per_unit" value="{{ old('margin_per_unit') }}" class="form-control">
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

@push('scripts')
<script>
(function () {
    const hargaMap = @json($hargaMap);
    const selMitra  = document.getElementById('sel-mitra');
    const selProduk = document.getElementById('sel-produk');
    const inpTahun  = document.getElementById('inp-tahun');
    const inpBulan  = document.getElementById('inp-bulan');

    function refresh() {
        const key = selMitra.value + ':' + inpTahun.value + ':' + inpBulan.value;
        const taken = hargaMap[key] || [];

        selProduk.querySelectorAll('option[value]').forEach(function (opt) {
            if (!opt.value) return;
            const id = parseInt(opt.value);
            const nama = opt.dataset.nama;
            if (taken.includes(id)) {
                opt.disabled = true;
                opt.textContent = nama + ' (sudah diset)';
                if (opt.selected) { opt.selected = false; selProduk.value = ''; }
            } else {
                opt.disabled = false;
                opt.textContent = nama;
            }
        });
    }

    [selMitra, inpTahun, inpBulan].forEach(el => el.addEventListener('change', refresh));
    refresh();
})();
</script>
@endpush

