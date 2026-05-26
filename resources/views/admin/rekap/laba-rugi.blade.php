@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Laba Rugi Bulanan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/rekap') }}">Rekap</a></li>
            <li class="breadcrumb-item active">Laba Rugi</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/rekap/laba-rugi') }}">
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-0">Tahun</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            @foreach ($tahunList as $t)
                                <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-9">
                        <button class="btn btn-outline-primary" type="submit">Tampilkan</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="small">Total Omzet {{ $tahun }}</div>
                                <div class="h5 mb-0">Rp {{ number_format($totals['omzet'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="small">Total Margin</div>
                                <div class="h5 mb-0">Rp {{ number_format($totals['margin'], 0, ',', '.') }}</div>
                                <div class="small">{{ number_format($totals['margin_pct'], 1) }}% dari omzet</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body">
                                <div class="small">Total Biaya</div>
                                <div class="h5 mb-0">Rp {{ number_format($totals['biaya'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <div class="small">Net Profit</div>
                                <div class="h5 mb-0">Rp {{ number_format($totals['net'], 0, ',', '.') }}</div>
                                <div class="small">{{ number_format($totals['net_pct'], 1) }}% dari omzet</div>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id="chartPL" height="80"></canvas>

                <div class="table-responsive mt-4">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Bulan</th>
                                <th class="text-end">Omzet</th>
                                <th class="text-end">Margin</th>
                                <th class="text-end">Margin %</th>
                                <th class="text-end">Biaya</th>
                                <th class="text-end">Net Profit</th>
                                <th class="text-end">Net %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                @php
                                    $netCls = $r['net'] >= 0 ? 'text-success' : 'text-danger';
                                @endphp
                                <tr>
                                    <td>{{ $r['bulan'] }}</td>
                                    <td class="text-end">{{ number_format($r['omzet'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($r['margin'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($r['margin_pct'], 1) }}%</td>
                                    <td class="text-end">{{ number_format($r['biaya'], 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold {{ $netCls }}">{{ number_format($r['net'], 0, ',', '.') }}</td>
                                    <td class="text-end {{ $netCls }}">{{ number_format($r['net_pct'], 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-semibold">
                                <td>Total {{ $tahun }}</td>
                                <td class="text-end">{{ number_format($totals['omzet'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($totals['margin'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($totals['margin_pct'], 1) }}%</td>
                                <td class="text-end">{{ number_format($totals['biaya'], 0, ',', '.') }}</td>
                                <td class="text-end {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totals['net'], 0, ',', '.') }}
                                </td>
                                <td class="text-end {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totals['net_pct'], 1) }}%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('chartPL');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chart['labels']),
            datasets: [
                { label: 'Omzet',  data: @json($chart['omzet']),  backgroundColor: 'rgba(13,110,253,.7)' },
                { label: 'Margin', data: @json($chart['margin']), backgroundColor: 'rgba(25,135,84,.7)' },
                { label: 'Biaya',  data: @json($chart['biaya']),  backgroundColor: 'rgba(255,193,7,.7)' },
                { label: 'Net',    type: 'line', data: @json($chart['net']), borderColor: '#212529', backgroundColor: '#212529', tension: .3, yAxisID: 'y' },
            ]
        },
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
