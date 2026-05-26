@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tambah Produksi Harian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/produksi') }}">Produksi Harian</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>

        @include('admin.partials.flash')

        <form method="post" action="{{ url('/admin/produksi') }}">
            @csrf
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', $tanggal->toDateString()) }}" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" value="{{ old('catatan') }}" class="form-control">
                        </div>
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <a class="btn btn-outline-secondary w-100" href="{{ url('/admin/produksi/create?tanggal='.$tanggal->toDateString().'&copy_from='.$prevDate) }}">
                                Salin dari {{ $prevDate }}
                            </a>
                        </div>
                    </div>
                    @if ($copyFrom)
                        <div class="alert alert-info mt-3 mb-0 py-2 small">Form diisi dari produksi tanggal <strong>{{ $copyFrom }}</strong> (silakan koreksi).</div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between gap-1">
                        <span>Detail Produksi per Produk</span>
                        <span class="small text-body-secondary">Stok awal dari sisa laporan mitra {{ $prevDate }}</span>
                    </div>
                    <div class="d-md-none mt-1 small text-body-secondary">Kolom <em>Tidak Layak</em> &amp; <em>Siap Jual</em> tersembunyi — tampil di layar lebar.</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end" style="min-width:90px;">
                                        Stok Awal
                                        <div class="fw-normal small text-body-secondary d-none d-sm-block">kembali dari mitra</div>
                                    </th>
                                    <th class="text-end" style="min-width:90px;">
                                        Produksi
                                        <div class="fw-normal small text-body-secondary d-none d-sm-block">baru hari ini</div>
                                    </th>
                                    <th class="text-end" style="min-width:110px;">
                                        Layak Jual
                                        <div class="fw-normal small text-body-secondary d-none d-sm-block">lolos cek, maks = stok awal</div>
                                    </th>
                                    <th class="text-end text-danger d-none d-md-table-cell">
                                        Tidak Layak
                                        <div class="fw-normal small">(terbuang)</div>
                                    </th>
                                    <th class="text-end d-none d-md-table-cell">
                                        Siap Jual
                                        <div class="fw-normal small text-body-secondary">produksi + layak</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produk as $p)
                                    @php $pf = $prefill[$p->id]; @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $p->nama }}</div>
                                            <div class="small text-body-secondary">{{ $p->kode_produk }} @if ($p->satuan) · {{ $p->satuan }} @endif</div>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" min="0" name="detail[{{ $p->id }}][stok_awal]"
                                                value="{{ old('detail.'.$p->id.'.stok_awal', $pf['stok_awal']) }}"
                                                class="form-control form-control-sm text-end inp-stok-awal">
                                        </td>
                                        <td class="p-1">
                                            <input type="number" min="0" name="detail[{{ $p->id }}][jumlah_produksi]"
                                                value="{{ old('detail.'.$p->id.'.jumlah_produksi', $pf['jumlah_produksi']) }}"
                                                class="form-control form-control-sm text-end inp-produksi">
                                        </td>
                                        <td class="p-1">
                                            <input type="number" min="0" name="detail[{{ $p->id }}][stok_layak_jual_kembali]"
                                                value="{{ old('detail.'.$p->id.'.stok_layak_jual_kembali', $pf['stok_layak_jual_kembali']) }}"
                                                class="form-control form-control-sm text-end inp-layak">
                                            <div class="small text-end mt-1 txt-maks text-body-secondary">maks: {{ $pf['stok_awal'] }}</div>
                                        </td>
                                        <td class="text-end txt-tidak-layak text-danger d-none d-md-table-cell">—</td>
                                        <td class="text-end fw-semibold txt-siap d-none d-md-table-cell">—</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-body-secondary d-md-none">Tidak ada produk aktif</td><td colspan="6" class="text-center text-body-secondary d-none d-md-table-cell">Tidak ada produk aktif</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a class="btn btn-outline-secondary" href="{{ url('/admin/produksi') }}">Kembali</a>
                </div>
            </div>
        </form>
    </div>

    <script>
    (function () {
        function updateRow(tr) {
            const awal = parseInt(tr.querySelector('.inp-stok-awal').value) || 0;
            const prod = parseInt(tr.querySelector('.inp-produksi').value) || 0;
            const layakInp = tr.querySelector('.inp-layak');
            const layak = parseInt(layakInp.value) || 0;
            const siapEl = tr.querySelector('.txt-siap');
            const tidakLayakEl = tr.querySelector('.txt-tidak-layak');
            const maksEl = tr.querySelector('.txt-maks');

            // update maks hint
            if (maksEl) maksEl.textContent = 'maks: ' + awal;

            // warn bila layak > stok awal
            if (layak > awal && awal > 0) {
                layakInp.classList.add('is-invalid');
            } else {
                layakInp.classList.remove('is-invalid');
            }

            const siap = prod + layak;
            siapEl.textContent = siap > 0 ? siap : '—';

            const tidakLayak = Math.max(0, awal - layak);
            if (tidakLayakEl) {
                tidakLayakEl.textContent = tidakLayak > 0 ? tidakLayak : '—';
                tidakLayakEl.classList.toggle('text-danger', tidakLayak > 0);
                tidakLayakEl.classList.toggle('text-body-secondary', tidakLayak === 0);
            }
        }

        const tbody = document.querySelector('tbody');
        if (!tbody) return;
        tbody.querySelectorAll('tr').forEach(updateRow);
        tbody.addEventListener('input', function (e) {
            const tr = e.target.closest('tr');
            if (tr) updateRow(tr);
        });
    })();
    </script>
@endsection
