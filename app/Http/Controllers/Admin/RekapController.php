<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    /**
     * Hub dashboard: KPI bulan berjalan + delta vs bulan lalu,
     * chart omzet 12 bulan, top 5 produk, top 5 mitra, alerts.
     */
    public function dashboard(Request $request)
    {
        $now = now();
        $startMonth = $now->copy()->startOfMonth()->toDateString();
        $endMonth = $now->copy()->endOfMonth()->toDateString();
        $prevStart = $now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $prevEnd = $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();

        $kpiNow = $this->aggregateKpi($startMonth, $endMonth);
        $kpiPrev = $this->aggregateKpi($prevStart, $prevEnd);

        $delta = [];
        foreach (['omzet', 'margin', 'biaya', 'net'] as $k) {
            $prev = (float) $kpiPrev[$k];
            $curr = (float) $kpiNow[$k];
            $delta[$k] = $prev == 0.0 ? null : (($curr - $prev) / abs($prev)) * 100;
        }

        // Chart 12 bulan terakhir
        $chartLabels = [];
        $chartOmzet = [];
        $chartMargin = [];
        $chartBiaya = [];
        $chartNet = [];
        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonthsNoOverflow($i);
            $s = $m->copy()->startOfMonth()->toDateString();
            $e = $m->copy()->endOfMonth()->toDateString();
            $k = $this->aggregateKpi($s, $e);
            $chartLabels[] = $namaBulan[$m->month] . ' ' . $m->format('y');
            $chartOmzet[] = (float) $k['omzet'];
            $chartMargin[] = (float) $k['margin'];
            $chartBiaya[] = (float) $k['biaya'];
            $chartNet[] = (float) $k['net'];
        }

        // Top 5 produk (bulan berjalan)
        $topProduk = DB::table('detail_laporan_penjualan_mitra as d')
            ->join('laporan_penjualan_mitra as lpm', 'lpm.id', '=', 'd.laporan_penjualan_mitra_id')
            ->join('produk as p', 'p.id', '=', 'd.produk_id')
            ->whereBetween('lpm.tanggal', [$startMonth, $endMonth])
            ->groupBy('p.id', 'p.nama')
            ->selectRaw('p.nama, SUM(d.jumlah_terjual) as terjual, SUM(d.total_penjualan) as omzet, SUM(d.total_margin) as margin')
            ->orderByDesc('omzet')
            ->limit(5)
            ->get();

        // Top 5 mitra (bulan berjalan)
        $topMitra = DB::table('detail_laporan_penjualan_mitra as d')
            ->join('laporan_penjualan_mitra as lpm', 'lpm.id', '=', 'd.laporan_penjualan_mitra_id')
            ->join('mitra as m', 'm.id', '=', 'lpm.mitra_id')
            ->whereBetween('lpm.tanggal', [$startMonth, $endMonth])
            ->groupBy('m.id', 'm.nama')
            ->selectRaw('m.nama, SUM(d.total_penjualan) as omzet, SUM(d.total_margin) as margin')
            ->orderByDesc('omzet')
            ->limit(5)
            ->get();

        // ALERTS
        // Piutang > 30 hari
        $cutoff = $now->copy()->subDays(30)->toDateString();
        $piutangOld = DB::table('laporan_penjualan_mitra as lpm')
            ->join('detail_laporan_penjualan_mitra as d', 'd.laporan_penjualan_mitra_id', '=', 'lpm.id')
            ->leftJoin('pembayaran_mitra_harian as p', function ($j) {
                $j->on('p.laporan_penjualan_mitra_id', '=', 'lpm.id')->where('p.status', 'confirmed');
            })
            ->where('lpm.tanggal', '<', $cutoff)
            ->groupBy('lpm.id', 'lpm.mitra_id', 'lpm.tanggal')
            ->selectRaw('lpm.mitra_id, SUM(d.total_penjualan) as omzet, COALESCE(SUM(p.jumlah_bayar),0) as bayar')
            ->havingRaw('SUM(d.total_penjualan) - COALESCE(SUM(p.jumlah_bayar),0) > 0')
            ->get();
        $alertPiutangTotal = $piutangOld->sum(function ($r) {
            return (float) $r->omzet - (float) $r->bayar;
        });
        $alertPiutangMitra = $piutangOld->pluck('mitra_id')->unique()->count();

        // Harga belum diset bulan ini
        $bulanIni = (int) $now->month;
        $tahunIni = (int) $now->year;
        $mitraAktifIds = DB::table('mitra')->where('is_aktif', 1)->pluck('id');
        $produkAktifIds = DB::table('produk')->where('is_aktif', 1)->pluck('id');
        $totalPair = $mitraAktifIds->count() * $produkAktifIds->count();
        $sudahSet = DB::table('harga_produk_mitra_bulanan')
            ->where('tahun', $tahunIni)
            ->where('bulan', $bulanIni)
            ->whereIn('mitra_id', $mitraAktifIds)
            ->whereIn('produk_id', $produkAktifIds)
            ->count();
        $alertHargaMissing = max(0, $totalPair - $sudahSet);

        return view('admin.rekap.dashboard', [
            'periode' => $namaBulan[$now->month] . ' ' . $now->year,
            'kpi' => $kpiNow,
            'delta' => $delta,
            'chart' => [
                'labels' => $chartLabels,
                'omzet' => $chartOmzet,
                'margin' => $chartMargin,
                'biaya' => $chartBiaya,
                'net' => $chartNet,
            ],
            'topProduk' => $topProduk,
            'topMitra' => $topMitra,
            'alerts' => [
                'piutang_total' => $alertPiutangTotal,
                'piutang_mitra' => $alertPiutangMitra,
                'harga_missing' => $alertHargaMissing,
            ],
        ]);
    }

    /**
     * Helper: agregasi KPI omzet/margin/biaya/net/pembayaran untuk range.
     */
    private function aggregateKpi(string $start, string $end): array
    {
        $om = DB::table('laporan_penjualan_mitra as lpm')
            ->join('detail_laporan_penjualan_mitra as d', 'd.laporan_penjualan_mitra_id', '=', 'lpm.id')
            ->whereBetween('lpm.tanggal', [$start, $end])
            ->selectRaw('COALESCE(SUM(d.total_penjualan),0) as omzet, COALESCE(SUM(d.total_margin),0) as margin')
            ->first();
        $biaya = (float) DB::table('biaya_harian')->whereBetween('tanggal', [$start, $end])->sum('total_biaya');
        $bayar = (float) DB::table('pembayaran_mitra_harian')->whereBetween('tanggal', [$start, $end])->where('status', 'confirmed')->sum('jumlah_bayar');
        $omzet = (float) ($om->omzet ?? 0);
        $margin = (float) ($om->margin ?? 0);
        return [
            'omzet' => $omzet,
            'margin' => $margin,
            'biaya' => $biaya,
            'net' => $margin - $biaya,
            'pembayaran' => $bayar,
        ];
    }

    /**
     * Rekap Piutang Mitra dengan aging bucket.
     */
    public function piutang(Request $request)
    {
        $today = now()->toDateString();

        // ambil semua laporan + total penjualan + pembayaran confirmed (kalau ada)
        $rows = DB::table('laporan_penjualan_mitra as lpm')
            ->join('mitra as m', 'm.id', '=', 'lpm.mitra_id')
            ->join('detail_laporan_penjualan_mitra as d', 'd.laporan_penjualan_mitra_id', '=', 'lpm.id')
            ->leftJoin('pembayaran_mitra_harian as p', function ($j) {
                $j->on('p.laporan_penjualan_mitra_id', '=', 'lpm.id')->where('p.status', 'confirmed');
            })
            ->groupBy('lpm.id', 'lpm.tanggal', 'm.id', 'm.nama')
            ->selectRaw('lpm.id as laporan_id, lpm.tanggal, m.id as mitra_id, m.nama, SUM(d.total_penjualan) as omzet, COALESCE(SUM(p.jumlah_bayar),0) as bayar')
            ->havingRaw('SUM(d.total_penjualan) - COALESCE(SUM(p.jumlah_bayar),0) > 0.01')
            ->orderBy('lpm.tanggal')
            ->get();

        // bucket aging per mitra
        $buckets = ['0-7' => 0, '8-14' => 0, '15-30' => 0, '>30' => 0];
        $perMitra = [];
        foreach ($rows as $r) {
            $sisa = (float) $r->omzet - (float) $r->bayar;
            $umur = Carbon::parse($r->tanggal)->diffInDays(Carbon::parse($today));
            if ($umur <= 7) {
                $bucket = '0-7';
            } elseif ($umur <= 14) {
                $bucket = '8-14';
            } elseif ($umur <= 30) {
                $bucket = '15-30';
            } else {
                $bucket = '>30';
            }
            $buckets[$bucket] += $sisa;

            $mid = (int) $r->mitra_id;
            if (!isset($perMitra[$mid])) {
                $perMitra[$mid] = [
                    'mitra_id' => $mid,
                    'nama' => $r->nama,
                    'total' => 0,
                    'b07' => 0,
                    'b814' => 0,
                    'b1530' => 0,
                    'b30plus' => 0,
                    'jml_laporan' => 0,
                    'laporan_terlama' => $r->tanggal,
                ];
            }
            $perMitra[$mid]['total'] += $sisa;
            $perMitra[$mid]['jml_laporan'] += 1;
            if ($r->tanggal < $perMitra[$mid]['laporan_terlama']) {
                $perMitra[$mid]['laporan_terlama'] = $r->tanggal;
            }
            $key = ['0-7' => 'b07', '8-14' => 'b814', '15-30' => 'b1530', '>30' => 'b30plus'][$bucket];
            $perMitra[$mid][$key] += $sisa;
        }

        usort($perMitra, fn($a, $b) => $b['total'] <=> $a['total']);

        return view('admin.rekap.piutang', [
            'buckets' => $buckets,
            'totalPiutang' => array_sum($buckets),
            'perMitra' => $perMitra,
            'detail' => $rows,
        ]);
    }

    /**
     * Rekap produk terlaris dengan sell-through rate.
     */
    public function produk(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $toDate = $to ? Carbon::parse($to)->startOfDay() : now()->endOfMonth();
        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        $start = $fromDate->toDateString();
        $end = $toDate->toDateString();

        // Penjualan dari laporan
        $jual = DB::table('detail_laporan_penjualan_mitra as d')
            ->join('laporan_penjualan_mitra as lpm', 'lpm.id', '=', 'd.laporan_penjualan_mitra_id')
            ->join('produk as p', 'p.id', '=', 'd.produk_id')
            ->whereBetween('lpm.tanggal', [$start, $end])
            ->groupBy('p.id', 'p.nama', 'p.satuan')
            ->selectRaw('p.id as produk_id, p.nama, p.satuan,
                SUM(d.jumlah_titip) as titip,
                SUM(d.jumlah_terjual) as terjual,
                SUM(d.sisa_barang) as sisa,
                SUM(d.stok_tidak_layak_jual) as tidak_layak,
                SUM(d.total_penjualan) as omzet,
                SUM(d.total_margin) as margin,
                COUNT(DISTINCT lpm.mitra_id) as jml_mitra')
            ->orderByDesc('omzet')
            ->get();

        $rows = $jual->map(function ($r) {
            $titip = (int) $r->titip;
            $terjual = (int) $r->terjual;
            $rate = $titip > 0 ? ($terjual / $titip) * 100 : 0;
            return [
                'nama' => $r->nama,
                'satuan' => $r->satuan,
                'titip' => $titip,
                'terjual' => $terjual,
                'sisa' => (int) $r->sisa,
                'tidak_layak' => (int) $r->tidak_layak,
                'sell_through' => $rate,
                'omzet' => (float) $r->omzet,
                'margin' => (float) $r->margin,
                'jml_mitra' => (int) $r->jml_mitra,
            ];
        })->all();

        $totals = [
            'titip' => array_sum(array_column($rows, 'titip')),
            'terjual' => array_sum(array_column($rows, 'terjual')),
            'sisa' => array_sum(array_column($rows, 'sisa')),
            'tidak_layak' => array_sum(array_column($rows, 'tidak_layak')),
            'omzet' => array_sum(array_column($rows, 'omzet')),
            'margin' => array_sum(array_column($rows, 'margin')),
        ];
        $totals['sell_through'] = $totals['titip'] > 0 ? ($totals['terjual'] / $totals['titip']) * 100 : 0;

        return view('admin.rekap.produk', [
            'from' => $start,
            'to' => $end,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }

    /**
     * Rekap Waste: produksi-side & mitra-side, dengan estimasi kerugian.
     */
    public function waste(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $toDate = $to ? Carbon::parse($to)->startOfDay() : now()->endOfMonth();
        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        $start = $fromDate->toDateString();
        $end = $toDate->toDateString();

        // Waste sisi produksi: stok_awal - stok_layak_jual_kembali (return mitra yg dibuang)
        $produksi = DB::table('detail_produksi_harian as d')
            ->join('produksi_harian as ph', 'ph.id', '=', 'd.produksi_harian_id')
            ->join('produk as p', 'p.id', '=', 'd.produk_id')
            ->whereBetween('ph.tanggal', [$start, $end])
            ->groupBy('p.id', 'p.nama', 'p.satuan')
            ->selectRaw('p.id as produk_id, p.nama, p.satuan,
                SUM(d.stok_awal) as total_awal,
                SUM(d.stok_layak_jual_kembali) as total_layak,
                GREATEST(SUM(d.stok_awal) - SUM(d.stok_layak_jual_kembali), 0) as waste_produksi')
            ->get()
            ->keyBy('produk_id');

        // Waste sisi mitra: stok_tidak_layak_jual dari laporan
        $mitra = DB::table('detail_laporan_penjualan_mitra as d')
            ->join('laporan_penjualan_mitra as lpm', 'lpm.id', '=', 'd.laporan_penjualan_mitra_id')
            ->join('produk as p', 'p.id', '=', 'd.produk_id')
            ->whereBetween('lpm.tanggal', [$start, $end])
            ->groupBy('p.id', 'p.nama', 'p.satuan')
            ->selectRaw('p.id as produk_id, p.nama, p.satuan,
                SUM(d.jumlah_titip) as total_titip,
                SUM(d.stok_tidak_layak_jual) as waste_mitra,
                AVG(d.harga_jual) as avg_harga')
            ->get()
            ->keyBy('produk_id');

        // Gabung
        $produkIds = $produksi->keys()->merge($mitra->keys())->unique();
        $rows = [];
        foreach ($produkIds as $pid) {
            $pr = $produksi->get($pid);
            $mt = $mitra->get($pid);
            $wasteProd = $pr ? (int) $pr->waste_produksi : 0;
            $wasteMitra = $mt ? (int) $mt->waste_mitra : 0;
            $totalWaste = $wasteProd + $wasteMitra;
            $titip = $mt ? (int) $mt->total_titip : 0;
            $avgHarga = $mt ? (float) $mt->avg_harga : 0.0;
            $wasteRate = $titip > 0 ? ($wasteMitra / $titip) * 100 : 0;
            $estimasiLost = $totalWaste * $avgHarga;
            $rows[] = [
                'nama' => $pr->nama ?? $mt->nama,
                'satuan' => $pr->satuan ?? $mt->satuan,
                'waste_produksi' => $wasteProd,
                'waste_mitra' => $wasteMitra,
                'total_waste' => $totalWaste,
                'titip' => $titip,
                'waste_rate' => $wasteRate,
                'avg_harga' => $avgHarga,
                'estimasi_lost' => $estimasiLost,
            ];
        }
        usort($rows, fn($a, $b) => $b['estimasi_lost'] <=> $a['estimasi_lost']);

        $totals = [
            'waste_produksi' => array_sum(array_column($rows, 'waste_produksi')),
            'waste_mitra' => array_sum(array_column($rows, 'waste_mitra')),
            'total_waste' => array_sum(array_column($rows, 'total_waste')),
            'estimasi_lost' => array_sum(array_column($rows, 'estimasi_lost')),
        ];

        return view('admin.rekap.waste', [
            'from' => $start,
            'to' => $end,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }

    /**
     * Laba Rugi Bulanan (12 bulan terakhir).
     */
    public function labaRugi(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $rows = [];
        $tot = ['omzet' => 0, 'margin' => 0, 'biaya' => 0, 'net' => 0];
        for ($m = 1; $m <= 12; $m++) {
            $s = Carbon::create($tahun, $m, 1)->startOfMonth()->toDateString();
            $e = Carbon::create($tahun, $m, 1)->endOfMonth()->toDateString();
            $k = $this->aggregateKpi($s, $e);
            $marginPct = $k['omzet'] > 0 ? ($k['margin'] / $k['omzet']) * 100 : 0;
            $netPct = $k['omzet'] > 0 ? ($k['net'] / $k['omzet']) * 100 : 0;
            $rows[] = [
                'bulan' => $namaBulan[$m],
                'bulan_num' => $m,
                'omzet' => $k['omzet'],
                'margin' => $k['margin'],
                'biaya' => $k['biaya'],
                'net' => $k['net'],
                'margin_pct' => $marginPct,
                'net_pct' => $netPct,
            ];
            $tot['omzet'] += $k['omzet'];
            $tot['margin'] += $k['margin'];
            $tot['biaya'] += $k['biaya'];
            $tot['net'] += $k['net'];
        }
        $tot['margin_pct'] = $tot['omzet'] > 0 ? ($tot['margin'] / $tot['omzet']) * 100 : 0;
        $tot['net_pct'] = $tot['omzet'] > 0 ? ($tot['net'] / $tot['omzet']) * 100 : 0;

        // chart data
        $chart = [
            'labels' => array_map(fn($r) => substr($r['bulan'], 0, 3), $rows),
            'omzet' => array_column($rows, 'omzet'),
            'margin' => array_column($rows, 'margin'),
            'biaya' => array_column($rows, 'biaya'),
            'net' => array_column($rows, 'net'),
        ];

        // tahun selector
        $minTahun = (int) (DB::table('laporan_penjualan_mitra')->min(DB::raw('YEAR(tanggal)')) ?? now()->year);
        $maxTahun = (int) max(now()->year, $tahun);
        $tahunList = range($maxTahun, $minTahun);

        return view('admin.rekap.laba-rugi', [
            'tahun' => $tahun,
            'tahunList' => $tahunList,
            'rows' => $rows,
            'totals' => $tot,
            'chart' => $chart,
        ]);
    }

    /**
     * Performa Mitra (B1): leaderboard dengan metrik lengkap.
     */
    public function mitra(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $toDate = $to ? Carbon::parse($to)->startOfDay() : now()->endOfMonth();
        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        $start = $fromDate->toDateString();
        $end = $toDate->toDateString();

        // Per mitra: titip, terjual, omzet, margin, frekuensi laporan
        $jual = DB::table('detail_laporan_penjualan_mitra as d')
            ->join('laporan_penjualan_mitra as lpm', 'lpm.id', '=', 'd.laporan_penjualan_mitra_id')
            ->join('mitra as m', 'm.id', '=', 'lpm.mitra_id')
            ->whereBetween('lpm.tanggal', [$start, $end])
            ->groupBy('m.id', 'm.nama')
            ->selectRaw('m.id as mitra_id, m.nama,
                SUM(d.jumlah_titip) as titip,
                SUM(d.jumlah_terjual) as terjual,
                SUM(d.stok_tidak_layak_jual) as tidak_layak,
                SUM(d.total_penjualan) as omzet,
                SUM(d.total_margin) as margin,
                COUNT(DISTINCT lpm.id) as jml_laporan,
                COUNT(DISTINCT d.produk_id) as jml_produk')
            ->get()
            ->keyBy('mitra_id');

        // Pembayaran confirmed
        $bayar = DB::table('pembayaran_mitra_harian')
            ->whereBetween('tanggal', [$start, $end])
            ->where('status', 'confirmed')
            ->groupBy('mitra_id')
            ->selectRaw('mitra_id, SUM(jumlah_bayar) as bayar')
            ->pluck('bayar', 'mitra_id');

        $rows = [];
        foreach ($jual as $mid => $r) {
            $titip = (int) $r->titip;
            $terjual = (int) $r->terjual;
            $sellThrough = $titip > 0 ? ($terjual / $titip) * 100 : 0;
            $omzet = (float) $r->omzet;
            $bayarVal = (float) ($bayar[$mid] ?? 0);
            $rows[] = [
                'mitra_id' => (int) $mid,
                'nama' => $r->nama,
                'titip' => $titip,
                'terjual' => $terjual,
                'tidak_layak' => (int) $r->tidak_layak,
                'sell_through' => $sellThrough,
                'omzet' => $omzet,
                'margin' => (float) $r->margin,
                'jml_laporan' => (int) $r->jml_laporan,
                'jml_produk' => (int) $r->jml_produk,
                'rata_omzet' => $r->jml_laporan > 0 ? $omzet / $r->jml_laporan : 0,
                'pembayaran' => $bayarVal,
                'selisih' => $omzet - $bayarVal,
            ];
        }
        usort($rows, fn($a, $b) => $b['omzet'] <=> $a['omzet']);

        $totals = [
            'titip' => array_sum(array_column($rows, 'titip')),
            'terjual' => array_sum(array_column($rows, 'terjual')),
            'tidak_layak' => array_sum(array_column($rows, 'tidak_layak')),
            'omzet' => array_sum(array_column($rows, 'omzet')),
            'margin' => array_sum(array_column($rows, 'margin')),
            'pembayaran' => array_sum(array_column($rows, 'pembayaran')),
            'selisih' => array_sum(array_column($rows, 'selisih')),
        ];
        $totals['sell_through'] = $totals['titip'] > 0 ? ($totals['terjual'] / $totals['titip']) * 100 : 0;

        return view('admin.rekap.mitra', [
            'from' => $start,
            'to' => $end,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }

    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth()->startOfDay();
        $toDate = $to ? Carbon::parse($to)->startOfDay() : now()->endOfMonth()->startOfDay();

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $start = $fromDate->toDateString();
        $end = $toDate->toDateString();

        $omzetByDate = DB::table('laporan_penjualan_mitra as lpm')
            ->join('detail_laporan_penjualan_mitra as d', 'd.laporan_penjualan_mitra_id', '=', 'lpm.id')
            ->whereBetween('lpm.tanggal', [$start, $end])
            ->groupBy('lpm.tanggal')
            ->selectRaw('lpm.tanggal as tanggal, SUM(d.total_penjualan) as omzet, SUM(d.total_margin) as margin')
            ->get()
            ->keyBy('tanggal');

        $biayaByDate = DB::table('biaya_harian')
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw('tanggal, total_biaya as biaya')
            ->get()
            ->keyBy('tanggal');

        $pembayaranByDate = DB::table('pembayaran_mitra_harian')
            ->whereBetween('tanggal', [$start, $end])
            ->where('status', 'confirmed')
            ->groupBy('tanggal')
            ->selectRaw('tanggal, SUM(jumlah_bayar) as pembayaran')
            ->get()
            ->keyBy('tanggal');

        $rows = [];
        $totOmzet = 0.0;
        $totMargin = 0.0;
        $totBiaya = 0.0;
        $totPembayaran = 0.0;

        foreach (CarbonPeriod::create($fromDate, $toDate) as $date) {
            $d = $date->toDateString();
            $omzet = (float) optional($omzetByDate->get($d))->omzet;
            $margin = (float) optional($omzetByDate->get($d))->margin;
            $biaya = (float) optional($biayaByDate->get($d))->biaya;
            $pembayaran = (float) optional($pembayaranByDate->get($d))->pembayaran;
            $net = $margin - $biaya;

            $totOmzet += $omzet;
            $totMargin += $margin;
            $totBiaya += $biaya;
            $totPembayaran += $pembayaran;

            $rows[] = [
                'tanggal' => $d,
                'omzet' => $omzet,
                'margin' => $margin,
                'biaya' => $biaya,
                'net' => $net,
                'pembayaran' => $pembayaran,
            ];
        }

        $mitraOmzet = DB::table('laporan_penjualan_mitra as lpm')
            ->join('mitra as m', 'm.id', '=', 'lpm.mitra_id')
            ->join('detail_laporan_penjualan_mitra as d', 'd.laporan_penjualan_mitra_id', '=', 'lpm.id')
            ->whereBetween('lpm.tanggal', [$start, $end])
            ->groupBy('m.id', 'm.nama')
            ->selectRaw('m.id as mitra_id, m.nama as nama, SUM(d.total_penjualan) as omzet, SUM(d.total_margin) as margin')
            ->orderBy('m.nama')
            ->get()
            ->keyBy('mitra_id');

        $mitraPembayaran = DB::table('pembayaran_mitra_harian as p')
            ->join('mitra as m', 'm.id', '=', 'p.mitra_id')
            ->whereBetween('p.tanggal', [$start, $end])
            ->where('p.status', 'confirmed')
            ->groupBy('m.id')
            ->selectRaw('m.id as mitra_id, SUM(p.jumlah_bayar) as pembayaran')
            ->get()
            ->keyBy('mitra_id');

        $mitraRows = [];
        foreach ($mitraOmzet as $mitraId => $r) {
            $omzet = (float) $r->omzet;
            $margin = (float) $r->margin;
            $pembayaran = (float) optional($mitraPembayaran->get($mitraId))->pembayaran;
            $mitraRows[] = [
                'mitra_id' => (int) $mitraId,
                'nama' => $r->nama,
                'omzet' => $omzet,
                'margin' => $margin,
                'pembayaran' => $pembayaran,
                'selisih' => $omzet - $pembayaran,
            ];
        }

        $totNet = $totMargin - $totBiaya;

        return view('admin.rekap.index', [
            'from' => $fromDate->toDateString(),
            'to' => $toDate->toDateString(),
            'rows' => $rows,
            'mitraRows' => $mitraRows,
            'totals' => [
                'omzet' => $totOmzet,
                'margin' => $totMargin,
                'biaya' => $totBiaya,
                'net' => $totNet,
                'pembayaran' => $totPembayaran,
            ],
        ]);
    }
}

