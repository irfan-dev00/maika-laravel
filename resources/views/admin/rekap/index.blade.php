@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Rekap & Laporan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Rekap</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/rekap') }}">
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
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/rekap') }}">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">Omzet</div>
                            <div class="card-footer small text-white">{{ number_format((float) $totals['omzet'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">Margin</div>
                            <div class="card-footer small text-white">{{ number_format((float) $totals['margin'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">Biaya</div>
                            <div class="card-footer small text-white">{{ number_format((float) $totals['biaya'], 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card bg-dark text-white">
                            <div class="card-body">Net (Margin - Biaya)</div>
                            <div class="card-footer small text-white">{{ number_format((float) $totals['net'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">Rekap Harian</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-end">Omzet</th>
                                        <th class="text-end">Margin</th>
                                        <th class="text-end">Biaya</th>
                                        <th class="text-end">Net</th>
                                        <th class="text-end">Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $r)
                                        <tr>
                                            <td class="text-nowrap">{{ $r['tanggal'] }}</td>
                                            <td class="text-end">{{ number_format((float) $r['omzet'], 2) }}</td>
                                            <td class="text-end">{{ number_format((float) $r['margin'], 2) }}</td>
                                            <td class="text-end">{{ number_format((float) $r['biaya'], 2) }}</td>
                                            <td class="text-end fw-semibold">{{ number_format((float) $r['net'], 2) }}</td>
                                            <td class="text-end">{{ number_format((float) $r['pembayaran'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">{{ number_format((float) $totals['omzet'], 2) }}</th>
                                        <th class="text-end">{{ number_format((float) $totals['margin'], 2) }}</th>
                                        <th class="text-end">{{ number_format((float) $totals['biaya'], 2) }}</th>
                                        <th class="text-end">{{ number_format((float) $totals['net'], 2) }}</th>
                                        <th class="text-end">{{ number_format((float) $totals['pembayaran'], 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">Rekap Per Mitra</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Mitra</th>
                                        <th class="text-end">Omzet</th>
                                        <th class="text-end">Margin</th>
                                        <th class="text-end">Pembayaran</th>
                                        <th class="text-end">Selisih (Omzet - Bayar)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mitraRows as $r)
                                        <tr>
                                            <td class="fw-semibold">{{ $r['nama'] }}</td>
                                            <td class="text-end">{{ number_format((float) $r['omzet'], 2) }}</td>
                                            <td class="text-end">{{ number_format((float) $r['margin'], 2) }}</td>
                                            <td class="text-end">{{ number_format((float) $r['pembayaran'], 2) }}</td>
                                            <td class="text-end fw-semibold">{{ number_format((float) $r['selisih'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-body-secondary">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

