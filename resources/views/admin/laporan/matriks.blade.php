@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Matriks Laporan Penjualan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/laporan') }}">Laporan Penjualan</a></li>
            <li class="breadcrumb-item active">Matriks {{ $tanggalStr }}</li>
        </ol>

        @include('admin.partials.flash')

        @include('admin.partials.date-navigator', [
            'navBaseUrl'    => url('/admin/laporan/matriks'),
            'navTanggal'    => $tanggal,
            'navTanggalStr' => $tanggalStr,
        ])

        @if ($pengirimanCount === 0)
            <div class="alert alert-warning">
                Belum ada pengiriman mitra pada tanggal ini. Buat pengiriman dulu lewat
                <a href="{{ url('/admin/pengiriman/matriks?tanggal='.$tanggalStr) }}">Matriks Pengiriman</a>.
            </div>
        @elseif ($produk->isEmpty() || $mitra->isEmpty())
            <div class="alert alert-warning">Tidak ada produk/mitra yang bisa ditampilkan.</div>
        @else
            <form method="post" action="{{ url('/admin/laporan/matriks') }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggalStr }}">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>Input Sisa Barang ({{ $mitra->count() }} mitra &times; {{ $produk->count() }} produk)</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="small text-body-secondary">Angka kecil = jumlah titip (read‑only)</span>
                            <a class="btn btn-outline-info btn-sm" href="{{ url('/admin/pengiriman/matriks?tanggal='.$tanggalStr) }}">
                                <i class="fa-solid fa-truck me-1"></i>Matriks Pengiriman
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 180px;">Mitra \ Produk</th>
                                        @foreach ($produk as $p)
                                            <th class="text-center" style="min-width: 120px;">
                                                {{ $p->nama }}
                                                @if (! isset($stokLayakMap[$p->id]))
                                                    <div class="small text-danger">produksi: -</div>
                                                @else
                                                    <div class="small text-body-secondary">layak: {{ $stokLayakMap[$p->id] }}</div>
                                                @endif
                                            </th>
                                        @endforeach
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mitra as $m)
                                        @php $l_exist = $laporan->get($m->id); @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $m->nama }}</div>
                                                <div class="small text-body-secondary">{{ $m->kode_mitra }}</div>
                                            </td>
                                            @foreach ($produk as $p)
                                                @php
                                                    $t = $titip[$m->id][$p->id] ?? null;
                                                    $val = $existing[$m->id][$p->id]['sisa_barang'] ?? null;
                                                    $terjual = $existing[$m->id][$p->id]['jumlah_terjual'] ?? null;
                                                @endphp
                                                <td class="p-1 {{ $t === null ? 'bg-body-tertiary' : '' }}">
                                                    @if ($t !== null)
                                                        <div class="d-flex flex-column">
                                                            <div class="small text-body-secondary text-end">titip: {{ $t }}</div>
                                                            <input type="number" min="0" max="{{ $t }}"
                                                                name="sisa[{{ $m->id }}][{{ $p->id }}]"
                                                                value="{{ old('sisa.'.$m->id.'.'.$p->id, $val) }}"
                                                                class="form-control form-control-sm text-end"
                                                                placeholder="sisa">
                                                            @if ($terjual !== null)
                                                                <div class="small text-success text-end">terjual: {{ $terjual }}</div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-center text-body-secondary small">—</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center small">
                                                @if ($l_exist)
                                                    <span class="badge bg-success">✓</span>
                                                @else
                                                    <span class="badge bg-secondary">baru</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Simpan Matriks</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/operasi?tanggal='.$tanggalStr) }}">Kembali ke Operasi Harian</a>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
