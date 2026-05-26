@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Piutang Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/rekap') }}">Rekap</a></li>
            <li class="breadcrumb-item active">Piutang</li>
        </ol>

        @include('admin.partials.flash')

        {{-- AGING BUCKETS --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="small">0 – 7 hari</div>
                        <div class="h4 mb-0">Rp {{ number_format((float) $buckets['0-7'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="small">8 – 14 hari</div>
                        <div class="h4 mb-0">Rp {{ number_format((float) $buckets['8-14'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="small">15 – 30 hari</div>
                        <div class="h4 mb-0">Rp {{ number_format((float) $buckets['15-30'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="small">&gt; 30 hari</div>
                        <div class="h4 mb-0">Rp {{ number_format((float) $buckets['>30'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-people-group me-2"></i>Piutang Per Mitra</span>
                <span class="text-muted small">Total piutang: <strong>Rp {{ number_format((float) $totalPiutang, 0, ',', '.') }}</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mitra</th>
                            <th class="text-end">0–7</th>
                            <th class="text-end">8–14</th>
                            <th class="text-end">15–30</th>
                            <th class="text-end">&gt;30</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Jml Laporan</th>
                            <th>Laporan Terlama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perMitra as $m)
                            <tr>
                                <td>{{ $m['nama'] }}</td>
                                <td class="text-end">{{ number_format($m['b07'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($m['b814'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($m['b1530'], 0, ',', '.') }}</td>
                                <td class="text-end text-danger fw-semibold">{{ number_format($m['b30plus'], 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($m['total'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ $m['jml_laporan'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($m['laporan_terlama'])->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">🎉 Tidak ada piutang outstanding</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-list me-2"></i>Detail Laporan Belum Lunas</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Mitra</th>
                            <th class="text-end">Omzet</th>
                            <th class="text-end">Sudah Bayar</th>
                            <th class="text-end">Sisa</th>
                            <th class="text-end">Umur</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detail as $r)
                            @php
                                $sisa = (float) $r->omzet - (float) $r->bayar;
                                $umur = \Carbon\Carbon::parse($r->tanggal)->diffInDays(now());
                                $cls = $umur > 30 ? 'text-danger fw-semibold' : ($umur > 14 ? 'text-warning' : '');
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                                <td>{{ $r->nama }}</td>
                                <td class="text-end">{{ number_format((float) $r->omzet, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format((float) $r->bayar, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($sisa, 0, ',', '.') }}</td>
                                <td class="text-end {{ $cls }}">{{ $umur }} hari</td>
                                <td>
                                    <a href="{{ url('/admin/pembayaran/create') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-plus"></i> Bayar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada laporan tertunggak</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
