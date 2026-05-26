@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Matriks Pengiriman Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/pengiriman') }}">Pengiriman Mitra</a></li>
            <li class="breadcrumb-item active">Matriks {{ $tanggalStr }}</li>
        </ol>

        @include('admin.partials.flash')

        @include('admin.partials.date-navigator', [
            'navBaseUrl'    => url('/admin/pengiriman/matriks'),
            'navTanggal'    => $tanggal,
            'navTanggalStr' => $tanggalStr,
        ])

        @if ($mitra->isEmpty() || $produk->isEmpty())
            <div class="alert alert-warning">
                Belum ada mitra aktif / produk dengan harga bulanan pada periode ini. Set harga di
                <a href="{{ url('/admin/harga') }}">Harga Bulanan</a> terlebih dahulu.
            </div>
        @else
            <form method="post" action="{{ url('/admin/pengiriman/matriks') }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggalStr }}">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>Jumlah Titip ({{ $mitra->count() }} mitra &times; {{ $produk->count() }} produk)</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-info">Periode harga: {{ str_pad((string) $bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}</span>
                            <span class="small text-body-secondary">Sel abu‑abu = harga belum diset</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 180px;">Mitra \ Produk</th>
                                        @foreach ($produk as $p)
                                            <th class="text-center" style="min-width: 110px;">{{ $p->nama }}</th>
                                        @endforeach
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mitra as $m)
                                        @php $p_exist = $pengiriman->get($m->id); @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $m->nama }}</div>
                                                <div class="small text-body-secondary">{{ $m->kode_mitra }}</div>
                                            </td>
                                            @foreach ($produk as $p)
                                                @php
                                                    $h = $hargaMap[$m->id][$p->id] ?? null;
                                                    $val = $existing[$m->id][$p->id] ?? null;
                                                @endphp
                                                <td class="p-1 {{ ! $h ? 'bg-body-tertiary' : '' }}">
                                                    @if ($h)
                                                        <input type="number" min="0" name="qty[{{ $m->id }}][{{ $p->id }}]"
                                                            value="{{ old('qty.'.$m->id.'.'.$p->id, $val) }}"
                                                            class="form-control form-control-sm text-end"
                                                            placeholder="0">
                                                        <div class="small text-body-secondary text-end">{{ number_format((float) $h->harga_jual, 0) }}</div>
                                                    @else
                                                        <div class="text-center text-body-secondary small">—</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center small">
                                                @if ($p_exist)
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
