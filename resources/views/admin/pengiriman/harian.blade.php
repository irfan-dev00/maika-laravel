@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
            <div>
                <h1 class="mb-0">Pengiriman Harian</h1>
                <div class="small text-body-secondary">Input semua mitra & produk dalam satu halaman, satu submit.</div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ url('/admin/pengiriman') }}">List</a>
                <a class="btn btn-outline-secondary" href="{{ url('/admin/pengiriman/matriks?tanggal='.$tanggalStr) }}">Matriks</a>
            </div>
        </div>
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ url('/admin/pengiriman') }}">Pengiriman</a></li>
            <li class="breadcrumb-item active">Harian {{ $tanggalStr }}</li>
        </ol>

        @include('admin.partials.flash')

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Toolbar tanggal --}}
        <form method="get" action="{{ url('/admin/pengiriman/harian') }}" class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggalStr }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary" type="submit">Muat</button>
                    </div>
                    <div class="col-auto ms-auto d-flex gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/pengiriman/harian?tanggal='.\Carbon\Carbon::parse($tanggalStr)->subDay()->toDateString()) }}">← Hari sebelumnya</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/pengiriman/harian?tanggal='.now()->toDateString()) }}">Hari ini</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ url('/admin/pengiriman/harian?tanggal='.\Carbon\Carbon::parse($tanggalStr)->addDay()->toDateString()) }}">Hari berikutnya →</a>
                        <a class="btn btn-sm btn-outline-info" href="{{ url('/admin/pengiriman/harian?tanggal='.$tanggalStr.'&copy_from='.$prevDate) }}">
                            Salin dari {{ $prevDate }}
                        </a>
                    </div>
                </div>
                @if ($copyInfo)
                    <div class="alert alert-info py-2 small mt-3 mb-0">
                        Disalin <strong>{{ $copyInfo['count'] }}</strong> mitra dari {{ $copyInfo['from'] }}. Periksa sebelum menyimpan.
                    </div>
                @endif
            </div>
        </form>

        <form method="post" action="{{ url('/admin/pengiriman/harian') }}" id="form-harian">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggalStr }}">

            <div id="sections-wrap">
                @foreach ($sections as $sectionIndex => $section)
                    @php
                        $mitraObj = $mitra->firstWhere('id', $section['mitra_id']);
                        $hargaForMitra = $hargaMap[$section['mitra_id']] ?? [];
                    @endphp
                    <div class="card mb-3 mitra-section" data-mitra-id="{{ $section['mitra_id'] }}">
                        <div class="card-header d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $section['is_existing'] ? 'success' : 'secondary' }}">
                                {{ $section['is_existing'] ? 'Tersimpan' : 'Baru' }}
                            </span>
                            <strong>{{ $mitraObj->nama ?? 'Mitra #'.$section['mitra_id'] }}</strong>
                            <span class="small text-body-secondary">
                                ({{ count($hargaForMitra) }} produk berharga periode ini)
                            </span>
                            <span class="ms-auto small text-body-secondary mitra-total"></span>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2 btn-remove-mitra" title="Hapus mitra dari tanggal ini">
                                <i class="fas fa-times"></i> Hapus mitra
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 align-middle table-rows">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50%">Produk</th>
                                            <th style="width:140px;" class="text-end">Jumlah Titip</th>
                                            <th style="width:140px;" class="text-end">Harga</th>
                                            <th style="width:140px;" class="text-end">Subtotal</th>
                                            <th style="width:60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($section['rows'] as $rowIndex => $row)
                                            <tr class="row-produk">
                                                <td>
                                                    <select name="mitras[{{ $section['mitra_id'] }}][rows][{{ $rowIndex }}][produk_id]"
                                                            class="form-select form-select-sm sel-produk" data-current="{{ $row['produk_id'] }}">
                                                                                                                {{-- diisi oleh JS --}}
                                                    </select>
                                                </td>
                                                <td class="p-1">
                                                    <input type="number" min="1" required
                                                        name="mitras[{{ $section['mitra_id'] }}][rows][{{ $rowIndex }}][jumlah_titip]"
                                                        value="{{ $row['jumlah_titip'] }}"
                                                        class="form-control form-control-sm text-end inp-qty">
                                                </td>
                                                <td class="text-end small txt-harga">-</td>
                                                <td class="text-end small txt-subtotal">-</td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Hapus baris">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer py-2">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-add-row">+ Tambah produk</button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Add mitra widget --}}
            <div class="card mb-3 border-primary border-1">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Tambah mitra ke tanggal ini</label>
                            <select id="select-add-mitra" class="form-select form-select-sm">
                                <option value="">— pilih mitra —</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="btn-add-mitra" class="btn btn-sm btn-primary">+ Tambah Mitra</button>
                        </div>
                        <div class="col-auto ms-auto small text-body-secondary align-self-center">
                            <span id="summary-total"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Simpan semua
                </button>
                <a class="btn btn-outline-secondary btn-lg" href="{{ url('/admin/pengiriman') }}">Batal</a>
                <span class="ms-auto small text-body-secondary align-self-center">
                    Mitra yang dihapus → header pengirimannya juga dihapus saat simpan.
                </span>
            </div>
        </form>
    </div>

    {{-- Templates --}}
    <template id="tpl-section">
        <div class="card mb-3 mitra-section" data-mitra-id="__MITRA_ID__">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="badge bg-secondary">Baru</span>
                <strong class="mitra-nama">__MITRA_NAMA__</strong>
                <span class="small text-body-secondary mitra-info"></span>
                <span class="ms-auto small text-body-secondary mitra-total"></span>
                <button type="button" class="btn btn-sm btn-outline-danger ms-2 btn-remove-mitra">
                    <i class="fas fa-times"></i> Hapus mitra
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle table-rows">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50%">Produk</th>
                                <th style="width:140px;" class="text-end">Jumlah Titip</th>
                                <th style="width:140px;" class="text-end">Harga</th>
                                <th style="width:140px;" class="text-end">Subtotal</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2">
                <button type="button" class="btn btn-sm btn-outline-primary btn-add-row">+ Tambah produk</button>
            </div>
        </div>
    </template>

    <template id="tpl-row">
        <tr class="row-produk">
            <td>
                <select class="form-select form-select-sm sel-produk" data-current=""></select>
            </td>
            <td class="p-1">
                <input type="number" min="1" required value="" class="form-control form-control-sm text-end inp-qty">
            </td>
            <td class="text-end small txt-harga">-</td>
            <td class="text-end small txt-subtotal">-</td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
    (function () {
        const PRODUK = @json($produk->map(fn ($p) => ['id' => $p->id, 'nama' => $p->nama, 'kode' => $p->kode_produk ?? ''])->values());
        const HARGA = @json((object) $hargaMap); // [mitraId][produkId] => {harga_jual, margin_per_unit}
        const MITRA = @json($mitra->map(fn ($m) => ['id' => $m->id, 'nama' => $m->nama])->values());
        const INITIAL_USED = @json(collect($sections)->pluck('mitra_id')->values());

        const wrap = document.getElementById('sections-wrap');
        const selectAdd = document.getElementById('select-add-mitra');
        const btnAdd = document.getElementById('btn-add-mitra');
        const tplSection = document.getElementById('tpl-section');
        const tplRow = document.getElementById('tpl-row');
        const summary = document.getElementById('summary-total');

        const usedMitra = new Set(INITIAL_USED);

        function fmt(n) {
            return Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }

        function refreshAddMitraDropdown() {
            const current = selectAdd.value;
            selectAdd.innerHTML = '<option value="">— pilih mitra —</option>';
            MITRA.forEach(m => {
                if (usedMitra.has(m.id)) return;
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.nama;
                selectAdd.appendChild(opt);
            });
            if (current) selectAdd.value = current;
        }

        function buildProdukOptions(selectEl, mitraId, currentValue, takenIds) {
            const hargaForMitra = HARGA[mitraId] || {};
            selectEl.innerHTML = '<option value="">— pilih produk —</option>';
            PRODUK.forEach(p => {
                const hasHarga = !!hargaForMitra[p.id];
                const taken = takenIds.has(p.id) && p.id !== Number(currentValue);
                const opt = document.createElement('option');
                opt.value = p.id;
                let label = p.nama;
                if (p.kode) label += ' (' + p.kode + ')';
                if (!hasHarga) label += ' — harga belum diset';
                opt.textContent = label;
                if (!hasHarga || taken) opt.disabled = true;
                if (Number(currentValue) === p.id) opt.selected = true;
                selectEl.appendChild(opt);
            });
        }

        function reindexSection(section) {
            const mitraId = section.dataset.mitraId;
            const rows = section.querySelectorAll('tbody tr.row-produk');
            rows.forEach((tr, idx) => {
                const sel = tr.querySelector('.sel-produk');
                const qty = tr.querySelector('.inp-qty');
                sel.name = `mitras[${mitraId}][rows][${idx}][produk_id]`;
                qty.name = `mitras[${mitraId}][rows][${idx}][jumlah_titip]`;
            });
        }

        function recomputeSection(section) {
            const mitraId = Number(section.dataset.mitraId);
            const hargaForMitra = HARGA[mitraId] || {};
            const takenIds = new Set();
            const rows = section.querySelectorAll('tbody tr.row-produk');
            rows.forEach(tr => {
                const sel = tr.querySelector('.sel-produk');
                const val = Number(sel.value);
                if (val > 0) takenIds.add(val);
            });

            let mitraTotal = 0;
            rows.forEach(tr => {
                const sel = tr.querySelector('.sel-produk');
                const qty = tr.querySelector('.inp-qty');
                const txtHarga = tr.querySelector('.txt-harga');
                const txtSub = tr.querySelector('.txt-subtotal');
                const currentVal = sel.value;
                buildProdukOptions(sel, mitraId, currentVal, takenIds);

                const pid = Number(sel.value);
                const h = pid && hargaForMitra[pid] ? hargaForMitra[pid].harga_jual : 0;
                const q = Number(qty.value || 0);
                const sub = h * q;
                txtHarga.textContent = h ? fmt(h) : '-';
                txtSub.textContent = sub ? fmt(sub) : '-';
                if (sub) mitraTotal += sub;
            });

            const mitraTotalEl = section.querySelector('.mitra-total');
            const totalRows = rows.length;
            mitraTotalEl.textContent = totalRows
                ? `${totalRows} produk · Rp ${fmt(mitraTotal)}`
                : 'belum ada produk';

            section._total = mitraTotal;
        }

        function recomputeAll() {
            let grand = 0;
            wrap.querySelectorAll('.mitra-section').forEach(s => {
                recomputeSection(s);
                grand += (s._total || 0);
            });
            summary.textContent = `Grand total: Rp ${fmt(grand)}`;
        }

        function addRow(section) {
            const tbody = section.querySelector('tbody');
            const clone = tplRow.content.cloneNode(true);
            tbody.appendChild(clone);
            reindexSection(section);
            recomputeSection(section);
        }

        function addSection(mitraId, mitraNama) {
            const clone = tplSection.content.cloneNode(true);
            const section = clone.querySelector('.mitra-section');
            section.dataset.mitraId = mitraId;
            section.querySelector('.mitra-nama').textContent = mitraNama;
            const hargaForMitra = HARGA[mitraId] || {};
            section.querySelector('.mitra-info').textContent = `(${Object.keys(hargaForMitra).length} produk berharga periode ini)`;
            wrap.appendChild(section);
            usedMitra.add(mitraId);
            // tambahkan 1 row kosong langsung
            addRow(section);
            refreshAddMitraDropdown();
        }

        // Inisialisasi: rebuild semua dropdown produk untuk section existing
        wrap.querySelectorAll('.mitra-section').forEach(section => {
            reindexSection(section);
            recomputeSection(section);
        });
        refreshAddMitraDropdown();
        recomputeAll();

        // Event delegation
        wrap.addEventListener('click', function (e) {
            const btnRemoveRow = e.target.closest('.btn-remove-row');
            if (btnRemoveRow) {
                const tr = btnRemoveRow.closest('tr');
                const section = btnRemoveRow.closest('.mitra-section');
                tr.remove();
                reindexSection(section);
                recomputeAll();
                return;
            }
            const btnAddRow = e.target.closest('.btn-add-row');
            if (btnAddRow) {
                const section = btnAddRow.closest('.mitra-section');
                addRow(section);
                recomputeAll();
                return;
            }
            const btnRemoveMitra = e.target.closest('.btn-remove-mitra');
            if (btnRemoveMitra) {
                const section = btnRemoveMitra.closest('.mitra-section');
                const mitraId = Number(section.dataset.mitraId);
                if (!confirm('Hapus mitra ini dari tanggal ' + @json($tanggalStr) + '?\n(Header pengiriman & seluruh detailnya akan dihapus saat Simpan.)')) return;
                section.remove();
                usedMitra.delete(mitraId);
                refreshAddMitraDropdown();
                recomputeAll();
            }
        });

        wrap.addEventListener('change', function (e) {
            if (e.target.classList.contains('sel-produk')) {
                const section = e.target.closest('.mitra-section');
                recomputeSection(section);
                recomputeAll();
            }
        });

        wrap.addEventListener('input', function (e) {
            if (e.target.classList.contains('inp-qty')) {
                const section = e.target.closest('.mitra-section');
                recomputeSection(section);
                recomputeAll();
            }
        });

        btnAdd.addEventListener('click', function () {
            const mid = Number(selectAdd.value);
            if (!mid) return;
            const m = MITRA.find(x => x.id === mid);
            if (!m) return;
            addSection(mid, m.nama);
            selectAdd.value = '';
            recomputeAll();
        });
    })();
    </script>
@endsection
