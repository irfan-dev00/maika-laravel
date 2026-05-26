@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard Rekap</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Rekap · Dashboard</li>
        </ol>

        @include('admin.partials.flash')

        <div class="mb-3 text-muted">Periode berjalan: <strong>{{ $periode }}</strong></div>

        {{-- KPI CARDS --}}
        @php
            $kpiCards = [
                ['key' => 'omzet', 'label' => 'Omzet', 'bg' => 'bg-primary'],
                ['key' => 'margin', 'label' => 'Margin', 'bg' => 'bg-success'],
                ['key' => 'biaya', 'label' => 'Biaya', 'bg' => 'bg-warning'],
                ['key' => 'net', 'label' => 'Net Profit', 'bg' => 'bg-dark'],
            ];
        @endphp
        <div class="row g-3 mb-4">
            @foreach ($kpiCards as $c)
                @php
                    $val = (float) $kpi[$c['key']];
                    $d = $delta[$c['key']];
                @endphp
                <div class="col-6 col-lg-3">
                    <div class="card {{ $c['bg'] }} text-white h-100">
                        <div class="card-body">
                            <div class="small">{{ $c['label'] }}</div>
                            <div class="h4 mb-0">Rp {{ number_format($val, 0, ',', '.') }}</div>
                        </div>
                        <div class="card-footer small text-white bg-transparent border-top-0">
                            @if ($d === null)
                                <span class="text-white-50">— vs bulan lalu</span>
                            @else
                                @php
                                    $arrow = $d >= 0 ? '▲' : '▼';
                                    $cls = $d >= 0 ? 'text-white' : 'text-white';
                                @endphp
                                <span class="{{ $cls }}">{{ $arrow }} {{ number_format(abs($d), 1) }}% vs bulan lalu</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CHART 12 BULAN --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-chart-area me-2"></i>Tren 12 Bulan Terakhir
            </div>
            <div class="card-body">
                <canvas id="chartTrend" height="100"></canvas>
            </div>
        </div>

        {{-- TOP PRODUK & TOP MITRA --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-boxes-stacked me-2"></i>Top 5 Produk (bulan ini)</span>
                        <a href="{{ url('/admin/rekap/produk') }}" class="small">Detail →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Produk</th>
                                    <th class="text-end">Terjual</th>
                                    <th class="text-end">Omzet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topProduk as $i => $r)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $r->nama }}</td>
                                        <td class="text-end">{{ number_format((int) $r->terjual) }}</td>
                                        <td class="text-end">Rp {{ number_format((float) $r->omzet, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-people-group me-2"></i>Top 5 Mitra (bulan ini)</span>
                        <a href="{{ url('/admin/rekap/harian') }}" class="small">Detail →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mitra</th>
                                    <th class="text-end">Omzet</th>
                                    <th class="text-end">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topMitra as $i => $r)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $r->nama }}</td>
                                        <td class="text-end">Rp {{ number_format((float) $r->omzet, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format((float) $r->margin, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERTS --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-triangle-exclamation me-2"></i>Quick Alerts</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa-solid fa-hand-holding-dollar text-danger me-2"></i>
                            Piutang &gt; 30 hari
                        </span>
                        <span>
                            <strong class="text-danger">Rp {{ number_format((float) $alerts['piutang_total'], 0, ',', '.') }}</strong>
                            dari <strong>{{ $alerts['piutang_mitra'] }}</strong> mitra
                            <a href="{{ url('/admin/rekap/piutang') }}" class="ms-2 btn btn-sm btn-outline-danger">Lihat</a>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fa-solid fa-tags text-warning me-2"></i>
                            Harga belum diset bulan ini
                        </span>
                        <span>
                            <strong class="text-warning">{{ $alerts['harga_missing'] }}</strong> pasangan mitra×produk
                            <a href="{{ url('/admin/harga/create') }}" class="ms-2 btn btn-sm btn-outline-warning">Set Harga</a>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('chartTrend');
    if (!ctx) return;
    const data = {
        labels: @json($chart['labels']),
        datasets: [
            { label: 'Omzet',  data: @json($chart['omzet']),  borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,.1)', tension: .3, fill: true },
            { label: 'Margin', data: @json($chart['margin']), borderColor: '#198754', backgroundColor: 'rgba(25,135,84,.1)', tension: .3, fill: true },
            { label: 'Biaya',  data: @json($chart['biaya']),  borderColor: '#ffc107', backgroundColor: 'rgba(255,193,7,.1)', tension: .3, fill: true },
            { label: 'Net',    data: @json($chart['net']),    borderColor: '#212529', backgroundColor: 'rgba(33,37,41,.1)',  tension: .3, fill: false, borderDash: [6,4] },
        ]
    };
    new Chart(ctx, {
        type: 'line',
        data,
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (c) => c.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(c.parsed.y)
                    }
                },
                legend: { position: 'bottom' }
            },
            scales: {
                y: {
                    ticks: { callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v) }
                }
            }
        }
    });
})();
</script>
@endpush
