@extends('layouts.admin')

@section('content')
    @php
        $prev  = $tanggal->copy()->subDay()->toDateString();
        $next  = $tanggal->copy()->addDay()->toDateString();
        $today = \Carbon\Carbon::today()->toDateString();

        $namaHari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $hariNama       = $namaHari[$tanggal->dayOfWeek];
        $bulanNama      = $namaBulan[$tanggal->month - 1];
        $tanggalFormatted = "$hariNama, {$tanggal->day} $bulanNama {$tanggal->year}";
        $isToday        = $tanggalStr === $today;
        $isLibur        = $kalender && $kalender->status === 'libur';

        // Progress summary
        $totalMitra   = count($mitraRows);
        $selesaiCount = 0;
        foreach ($mitraRows as $r) {
            if ($r['pengiriman'] && $r['laporan'] && $r['pembayaran']) $selesaiCount++;
        }
        $pctSelesai = $totalMitra > 0 ? round($selesaiCount / $totalMitra * 100) : 0;

        // KPI progress
        $pctPengiriman = $summary['pengiriman_total'] > 0
            ? round($summary['pengiriman_done'] / $summary['pengiriman_total'] * 100) : 0;
        $pctLaporan = $summary['laporan_total'] > 0
            ? round($summary['laporan_done'] / $summary['laporan_total'] * 100) : 0;
    @endphp

    <div class="container-fluid px-4">
        <h1 class="mt-4">Operasi Harian</h1>
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item active">{{ $tanggalFormatted }}</li>
        </ol>

        @include('admin.partials.flash')

        {{-- ===== DATE NAVIGATOR ===== --}}
        <div class="card mb-4 {{ $isLibur ? 'border-danger' : '' }}">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    {{-- Prev --}}
                    <a href="{{ url('/admin/operasi?tanggal='.$prev) }}"
                       class="btn btn-outline-secondary btn-sm"
                       title="{{ $prev }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    {{-- Tanggal besar --}}
                    <form method="get" action="{{ url('/admin/operasi') }}" class="d-flex align-items-center gap-2">
                        <div class="text-center" style="min-width:180px">
                            <div class="fw-bold fs-5 lh-1">
                                {{ $hariNama }}, {{ $tanggal->day }} {{ $bulanNama }}
                            </div>
                            <div class="text-body-secondary small">{{ $tanggal->year }}</div>
                        </div>
                        <input type="date" name="tanggal" value="{{ $tanggalStr }}"
                               class="form-control form-control-sm" style="width:150px"
                               onchange="this.form.submit()">
                    </form>

                    {{-- Next --}}
                    <a href="{{ url('/admin/operasi?tanggal='.$next) }}"
                       class="btn btn-outline-secondary btn-sm"
                       title="{{ $next }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    @if (! $isToday)
                        <a href="{{ url('/admin/operasi?tanggal='.$today) }}"
                           class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-calendar-day me-1"></i>Hari Ini
                        </a>
                    @endif

                    {{-- Kalender badge --}}
                    @if ($kalender)
                        <span class="badge bg-{{ $isLibur ? 'danger' : 'success' }} ms-auto">
                            <i class="fa-solid fa-{{ $isLibur ? 'ban' : 'circle-check' }} me-1"></i>
                            {{ ucfirst($kalender->status) }}
                            @if ($kalender->keterangan) — {{ $kalender->keterangan }} @endif
                        </span>
                    @else
                        <span class="badge bg-warning text-dark ms-auto">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>Kalender belum diset
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== BANNER LIBUR ===== --}}
        @if ($isLibur)
            <div class="alert alert-danger d-flex align-items-center gap-3 mb-4" role="alert">
                <i class="fa-solid fa-ban fa-2x flex-shrink-0"></i>
                <div>
                    <strong>Hari Libur</strong> &mdash; {{ $tanggalFormatted }}
                    @if ($kalender->keterangan) &mdash; {{ $kalender->keterangan }} @endif
                    <div class="small mt-1">Tidak ada operasional pada tanggal ini.</div>
                </div>
            </div>
        @endif

        {{-- ===== KPI CARDS ===== --}}
        <div class="row g-3 mb-4">

            {{-- Produksi --}}
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-body-secondary text-uppercase fw-semibold mb-1">Produksi</div>
                            @if ($produksi)
                                <div class="fs-5 fw-bold text-success">{{ $produksi->detail_count }} produk</div>
                                <div class="small text-success mt-1"><i class="fa-solid fa-circle-check me-1"></i>Selesai</div>
                            @else
                                <div class="fs-5 fw-bold text-body-secondary">Belum dibuat</div>
                                <div class="small text-warning mt-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Perlu dibuat</div>
                            @endif
                        </div>
                        <div class="ms-3">
                            <i class="fa-solid fa-industry fa-2x {{ $produksi ? 'text-success' : 'text-body-secondary' }} opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        @if ($produksi)
                            <a class="btn btn-sm btn-outline-success w-100" href="{{ url('/admin/produksi/'.$produksi->id.'/edit') }}">
                                <i class="fa-solid fa-pen me-1"></i>Buka
                            </a>
                        @else
                            <a class="btn btn-sm btn-success w-100" href="{{ url('/admin/produksi/create?tanggal='.$tanggalStr) }}">
                                <i class="fa-solid fa-plus me-1"></i>Buat Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pengiriman --}}
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="small text-body-secondary text-uppercase fw-semibold mb-1">Pengiriman</div>
                            @if ($summary['pengiriman_total'] > 0)
                                <div class="fs-5 fw-bold {{ $summary['pengiriman_done'] === $summary['pengiriman_total'] ? 'text-success' : 'text-primary' }}">
                                    {{ $summary['pengiriman_done'] }} / {{ $summary['pengiriman_total'] }}
                                    <span class="fs-6 fw-normal text-body-secondary">mitra</span>
                                </div>
                                <div class="progress mt-2" style="height:5px">
                                    <div class="progress-bar bg-primary" style="width:{{ $pctPengiriman }}%"></div>
                                </div>
                                <div class="small text-body-secondary mt-1">{{ $pctPengiriman }}% selesai</div>
                            @else
                                <div class="fs-5 fw-bold text-body-secondary">Belum ada mitra aktif</div>
                                <div class="small text-warning mt-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Data mitra diperlukan</div>
                            @endif
                        </div>
                        <div class="ms-3">
                            <i class="fa-solid fa-truck fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        @if ($summary['pengiriman_total'] > 0)
                            <a class="btn btn-sm btn-outline-primary w-100" href="{{ url('/admin/pengiriman/matriks?tanggal='.$tanggalStr) }}">
                                <i class="fa-solid fa-table-cells me-1"></i>Matriks
                            </a>
                        @else
                            <a class="btn btn-sm btn-outline-secondary w-100" href="{{ url('/admin/mitra') }}">
                                <i class="fa-solid fa-store me-1"></i>Kelola Mitra
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Laporan --}}
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="small text-body-secondary text-uppercase fw-semibold mb-1">Laporan Penjualan</div>
                            @if ($summary['laporan_total'] > 0)
                                <div class="fs-5 fw-bold {{ $summary['laporan_done'] === $summary['laporan_total'] ? 'text-success' : 'text-info' }}">
                                    {{ $summary['laporan_done'] }} / {{ $summary['laporan_total'] }}
                                    <span class="fs-6 fw-normal text-body-secondary">mitra</span>
                                </div>
                                <div class="progress mt-2" style="height:5px">
                                    <div class="progress-bar bg-info" style="width:{{ $pctLaporan }}%"></div>
                                </div>
                                <div class="small text-body-secondary mt-1">{{ $pctLaporan }}% selesai</div>
                            @else
                                <div class="fs-5 fw-bold text-body-secondary">Belum ada mitra aktif</div>
                                <div class="small text-warning mt-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Data mitra diperlukan</div>
                            @endif
                        </div>
                        <div class="ms-3">
                            <i class="fa-solid fa-file-invoice-dollar fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        @if ($summary['laporan_total'] > 0)
                            <a class="btn btn-sm btn-outline-info w-100" href="{{ url('/admin/laporan/matriks?tanggal='.$tanggalStr) }}">
                                <i class="fa-solid fa-table-cells me-1"></i>Matriks
                            </a>
                        @else
                            <a class="btn btn-sm btn-outline-secondary w-100" href="{{ url('/admin/mitra') }}">
                                <i class="fa-solid fa-store me-1"></i>Kelola Mitra
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Biaya --}}
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-body-secondary text-uppercase fw-semibold mb-1">Biaya Harian</div>
                            @if ($biaya)
                                <div class="fs-5 fw-bold text-success">Rp {{ number_format((float) $biaya->total_biaya, 0, ',', '.') }}</div>
                                <div class="small text-success mt-1"><i class="fa-solid fa-circle-check me-1"></i>Selesai</div>
                            @else
                                <div class="fs-5 fw-bold text-body-secondary">Belum dibuat</div>
                                <div class="small text-warning mt-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Perlu dibuat</div>
                            @endif
                        </div>
                        <div class="ms-3">
                            <i class="fa-solid fa-receipt fa-2x {{ $biaya ? 'text-success' : 'text-body-secondary' }} opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        @if ($biaya)
                            <a class="btn btn-sm btn-outline-success w-100" href="{{ url('/admin/biaya/'.$biaya->id.'/edit') }}">
                                <i class="fa-solid fa-pen me-1"></i>Buka
                            </a>
                        @else
                            <a class="btn btn-sm btn-success w-100" href="{{ url('/admin/biaya/create?tanggal='.$tanggalStr) }}">
                                <i class="fa-solid fa-plus me-1"></i>Buat Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABEL STATUS MITRA ===== --}}
        <div class="card mb-4 {{ $isLibur ? 'border-danger' : '' }}">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 {{ $isLibur ? 'bg-danger text-white' : '' }}">
                <div>
                    <i class="fa-solid fa-people-group me-2"></i>
                    <strong>Status per Mitra</strong>
                    <span class="ms-2 small {{ $isLibur ? 'text-white-50' : 'text-body-secondary' }}">{{ $tanggalStr }}</span>
                    @if ($isLibur)
                        <span class="badge bg-white text-danger ms-2">LIBUR{{ $kalender->keterangan ? ' — '.$kalender->keterangan : '' }}</span>
                    @endif
                </div>
                <span class="small {{ $isLibur ? 'text-white-50' : 'text-body-secondary' }}">
                    Harga: {{ str_pad((string) $bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}
                </span>
            </div>

            {{-- Progress bar keseluruhan --}}
            @if ($totalMitra > 0)
                <div class="px-3 pt-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">
                            {{ $selesaiCount }} dari {{ $totalMitra }} mitra selesai hari ini
                        </span>
                        <span class="small text-body-secondary">{{ $pctSelesai }}%</span>
                    </div>
                    <div class="progress mb-3" style="height:8px">
                        <div class="progress-bar {{ $pctSelesai === 100 ? 'bg-success' : ($pctSelesai >= 50 ? 'bg-primary' : 'bg-warning') }}"
                             role="progressbar" style="width:{{ $pctSelesai }}%"></div>
                    </div>
                </div>
            @endif

            <div class="card-body pt-0">
                @if (empty($mitraRows))
                    <p class="text-body-secondary mb-0">Belum ada mitra aktif.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mitra</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Pengiriman</th>
                                    <th class="text-center">Laporan</th>
                                    <th class="text-center">Pembayaran</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mitraRows as $row)
                                    @php
                                        $m       = $row['mitra'];
                                        $peng    = $row['pengiriman'];
                                        $lap     = $row['laporan'];
                                        $bay     = $row['pembayaran'];
                                        $missing = $row['harga_missing'];
                                        $allDone = $peng && $lap && $bay;
                                    @endphp
                                    <tr class="{{ $allDone ? 'table-success' : '' }}">
                                        <td>
                                            <div class="fw-semibold">{{ $m->nama }}</div>
                                            <div class="small text-body-secondary">{{ $m->kode_mitra }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if (empty($missing))
                                                <span class="badge bg-success rounded-pill" title="Semua harga sudah diset">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill"
                                                      data-bs-toggle="popover"
                                                      data-bs-trigger="hover focus"
                                                      data-bs-title="Harga belum diset"
                                                      data-bs-content="{{ implode(', ', array_column((array)$missing, 'nama')) }}"
                                                      style="cursor:pointer">
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ count($missing) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($peng)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fa-solid fa-check me-1"></i>{{ $peng->detail_count }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($lap)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fa-solid fa-check me-1"></i>{{ $lap->detail_count }}
                                                </span>
                                            @elseif ($peng)
                                                <span class="badge bg-warning text-dark rounded-pill">Siap</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($bay)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fa-solid fa-check me-1"></i>{{ number_format((float) $bay->jumlah_bayar, 0, ',', '.') }}
                                                </span>
                                            @elseif ($lap)
                                                <span class="badge bg-warning text-dark rounded-pill">
                                                    <i class="fa-solid fa-clock me-1"></i>Belum
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                @if ($peng)
                                                    <a class="btn btn-sm btn-outline-primary"
                                                       href="{{ url('/admin/pengiriman/'.$peng->id.'/edit') }}"
                                                       title="Edit Pengiriman" data-bs-toggle="tooltip">
                                                        <i class="fa-solid fa-truck"></i>
                                                    </a>
                                                @endif
                                                @if ($lap)
                                                    <a class="btn btn-sm btn-outline-primary"
                                                       href="{{ url('/admin/laporan/'.$lap->id.'/edit') }}"
                                                       title="Edit Laporan" data-bs-toggle="tooltip">
                                                        <i class="fa-solid fa-file-invoice"></i>
                                                    </a>
                                                @endif
                                                @if ($lap && ! $bay)
                                                    <a class="btn btn-sm btn-success"
                                                       href="{{ url('/admin/pembayaran/create?laporan_id='.$lap->id) }}"
                                                       title="Bayar" data-bs-toggle="tooltip">
                                                        <i class="fa-solid fa-money-bill-wave"></i>
                                                    </a>
                                                @endif
                                                @if ($bay)
                                                    <a class="btn btn-sm btn-outline-secondary"
                                                       href="{{ url('/admin/pembayaran/'.$bay->id.'/edit') }}"
                                                       title="Lihat Pembayaran" data-bs-toggle="tooltip">
                                                        <i class="fa-solid fa-receipt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    // Init tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
    // Init popovers
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el);
    });
})();
</script>
@endpush
