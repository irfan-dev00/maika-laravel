@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Waste & Kerugian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/rekap') }}">Rekap</a></li>
            <li class="breadcrumb-item active">Waste</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/rekap/waste') }}">
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-0">Dari</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-0">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6 d-flex gap-2">
                        <button class="btn btn-outline-primary" type="submit">Tampilkan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/rekap/waste') }}">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body">
                                <div class="small">Waste Sisi Produksi</div>
                                <div class="h5 mb-0">{{ number_format($totals['waste_produksi']) }} unit</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="small">Waste Sisi Mitra</div>
                                <div class="h5 mb-0">{{ number_format($totals['waste_mitra']) }} unit</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <div class="small">Total Waste</div>
                                <div class="h5 mb-0">{{ number_format($totals['total_waste']) }} unit</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-secondary text-white h-100">
                            <div class="card-body">
                                <div class="small">Estimasi Potensi Hilang</div>
                                <div class="h5 mb-0">Rp {{ number_format($totals['estimasi_lost'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info small mb-3">
                    <strong>Definisi:</strong>
                    <ul class="mb-0">
                        <li><strong>Waste Sisi Produksi</strong> = stok return mitra yang tidak bisa dipakai ulang (<code>stok_awal − stok_layak_jual_kembali</code>).</li>
                        <li><strong>Waste Sisi Mitra</strong> = stok tidak layak jual saat laporan mitra (<code>stok_tidak_layak_jual</code>).</li>
                        <li><strong>Estimasi Potensi Hilang</strong> = total waste × rata-rata harga jual produk pada rentang ini (asumsi semua waste bisa terjual).</li>
                    </ul>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th class="text-end">Waste Produksi</th>
                                <th class="text-end">Waste Mitra</th>
                                <th class="text-end">Total Waste</th>
                                <th class="text-end">Titip</th>
                                <th class="text-end">Waste Rate</th>
                                <th class="text-end">Avg Harga</th>
                                <th class="text-end">Est. Hilang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $i => $r)
                                @php
                                    $wr = (float) $r['waste_rate'];
                                    $wrCls = $wr >= 20 ? 'bg-danger' : ($wr >= 10 ? 'bg-warning text-dark' : 'bg-success');
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $r['nama'] }}</div>
                                        <div class="small text-muted">{{ $r['satuan'] }}</div>
                                    </td>
                                    <td class="text-end">{{ number_format($r['waste_produksi']) }}</td>
                                    <td class="text-end">{{ number_format($r['waste_mitra']) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($r['total_waste']) }}</td>
                                    <td class="text-end">{{ number_format($r['titip']) }}</td>
                                    <td class="text-end">
                                        @if ($r['titip'] > 0)
                                            <span class="badge {{ $wrCls }}">{{ number_format($wr, 1) }}%</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($r['avg_harga'], 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($r['estimasi_lost'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data waste di rentang ini</td></tr>
                            @endforelse
                        </tbody>
                        @if (count($rows))
                            <tfoot class="table-light">
                                <tr class="fw-semibold">
                                    <td colspan="2">Total</td>
                                    <td class="text-end">{{ number_format($totals['waste_produksi']) }}</td>
                                    <td class="text-end">{{ number_format($totals['waste_mitra']) }}</td>
                                    <td class="text-end">{{ number_format($totals['total_waste']) }}</td>
                                    <td colspan="3"></td>
                                    <td class="text-end text-danger">Rp {{ number_format($totals['estimasi_lost'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
