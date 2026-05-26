@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Produk Terlaris</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/rekap') }}">Rekap</a></li>
            <li class="breadcrumb-item active">Produk Terlaris</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/rekap/produk') }}">
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
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/rekap/produk') }}">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Titip</div>
                            <div class="h5 mb-0">{{ number_format($totals['titip']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Terjual</div>
                            <div class="h5 mb-0">{{ number_format($totals['terjual']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Sell-Through</div>
                            <div class="h5 mb-0">{{ number_format($totals['sell_through'], 1) }}%</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Total Omzet</div>
                            <div class="h5 mb-0">Rp {{ number_format($totals['omzet'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th class="text-end">Titip</th>
                                <th class="text-end">Terjual</th>
                                <th class="text-end">Sisa</th>
                                <th class="text-end">Tdk Layak</th>
                                <th class="text-end">Sell-Through</th>
                                <th class="text-end">Omzet</th>
                                <th class="text-end">Margin</th>
                                <th class="text-end">Mitra</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $i => $r)
                                @php
                                    $st = (float) $r['sell_through'];
                                    $stCls = $st >= 80 ? 'bg-success' : ($st >= 50 ? 'bg-warning text-dark' : 'bg-danger');
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $r['nama'] }}</div>
                                        <div class="small text-muted">{{ $r['satuan'] }}</div>
                                    </td>
                                    <td class="text-end">{{ number_format($r['titip']) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($r['terjual']) }}</td>
                                    <td class="text-end">{{ number_format($r['sisa']) }}</td>
                                    <td class="text-end text-danger">{{ number_format($r['tidak_layak']) }}</td>
                                    <td class="text-end">
                                        <span class="badge {{ $stCls }}">{{ number_format($st, 1) }}%</span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($r['omzet'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($r['margin'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ $r['jml_mitra'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data penjualan di rentang ini</td></tr>
                            @endforelse
                        </tbody>
                        @if (count($rows))
                            <tfoot class="table-light">
                                <tr class="fw-semibold">
                                    <td colspan="2">Total</td>
                                    <td class="text-end">{{ number_format($totals['titip']) }}</td>
                                    <td class="text-end">{{ number_format($totals['terjual']) }}</td>
                                    <td class="text-end">{{ number_format($totals['sisa']) }}</td>
                                    <td class="text-end">{{ number_format($totals['tidak_layak']) }}</td>
                                    <td class="text-end">{{ number_format($totals['sell_through'], 1) }}%</td>
                                    <td class="text-end">Rp {{ number_format($totals['omzet'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($totals['margin'], 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
