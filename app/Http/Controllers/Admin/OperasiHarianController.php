<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiayaHarian;
use App\Models\HargaProdukMitraBulanan;
use App\Models\KalenderOperasional;
use App\Models\LaporanPenjualanMitra;
use App\Models\Mitra;
use App\Models\PembayaranMitraHarian;
use App\Models\PengirimanMitra;
use App\Models\Produk;
use App\Models\ProduksiHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperasiHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggalInput = trim((string) $request->query('tanggal', ''));

        try {
            $tanggal = $tanggalInput !== ''
                ? Carbon::parse($tanggalInput)->startOfDay()
                : Carbon::today();
        } catch (\Throwable $e) {
            $tanggal = Carbon::today();
        }

        $tanggalStr = $tanggal->toDateString();
        $tahun = (int) $tanggal->format('Y');
        $bulan = (int) $tanggal->format('n');

        $kalender = KalenderOperasional::query()->where('tanggal', $tanggalStr)->first();

        $mitraAktif = Mitra::query()
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        $produkAktif = Produk::query()
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        // Produksi
        $produksi = ProduksiHarian::query()
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->first();

        // Pengiriman per mitra
        $pengirimanList = PengirimanMitra::query()
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->get()
            ->keyBy('mitra_id');

        // Laporan per mitra
        $laporanList = LaporanPenjualanMitra::query()
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->get()
            ->keyBy('mitra_id');

        // Pembayaran per laporan
        $laporanIds = $laporanList->pluck('id')->all();
        $pembayaranByLaporan = $laporanIds
            ? PembayaranMitraHarian::query()
                ->whereIn('laporan_penjualan_mitra_id', $laporanIds)
                ->get()
                ->keyBy('laporan_penjualan_mitra_id')
            : collect();

        // Biaya
        $biaya = BiayaHarian::query()
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->first();

        // Cek harga bulanan: pasangan (mitra aktif, produk aktif) yang belum punya harga.
        $hargaSet = HargaProdukMitraBulanan::query()
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->whereIn('mitra_id', $mitraAktif->pluck('id'))
            ->whereIn('produk_id', $produkAktif->pluck('id'))
            ->get()
            ->map(fn ($h) => $h->mitra_id.':'.$h->produk_id)
            ->all();
        $hargaSet = array_flip($hargaSet);

        $hargaMissing = []; // [mitra_id => [produk, ...]]
        foreach ($mitraAktif as $m) {
            foreach ($produkAktif as $p) {
                $key = $m->id.':'.$p->id;
                if (! isset($hargaSet[$key])) {
                    $hargaMissing[$m->id][] = $p;
                }
            }
        }

        // Susun baris status mitra
        $mitraRows = [];
        foreach ($mitraAktif as $m) {
            $peng = $pengirimanList->get($m->id);
            $lap = $laporanList->get($m->id);
            $bay = $lap ? $pembayaranByLaporan->get($lap->id) : null;

            $mitraRows[] = [
                'mitra' => $m,
                'pengiriman' => $peng,
                'laporan' => $lap,
                'pembayaran' => $bay,
                'harga_missing' => $hargaMissing[$m->id] ?? [],
            ];
        }

        $summary = [
            'pengiriman_done' => collect($mitraRows)->where('pengiriman')->count(),
            'pengiriman_total' => count($mitraRows),
            'laporan_done' => collect($mitraRows)->where('laporan')->count(),
            'laporan_total' => count($mitraRows),
            'pembayaran_done' => collect($mitraRows)->where('pembayaran')->count(),
            'pembayaran_total' => collect($mitraRows)->where('laporan')->count(),
        ];

        return view('admin.operasi.index', [
            'tanggal' => $tanggal,
            'tanggalStr' => $tanggalStr,
            'kalender' => $kalender,
            'produksi' => $produksi,
            'biaya' => $biaya,
            'mitraRows' => $mitraRows,
            'summary' => $summary,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
}
