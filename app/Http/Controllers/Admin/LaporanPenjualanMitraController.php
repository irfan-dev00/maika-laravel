<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDetailLaporanPenjualanMitraRequest;
use App\Http\Requests\Admin\StoreLaporanPenjualanMitraRequest;
use App\Http\Requests\Admin\UpdateDetailLaporanPenjualanMitraRequest;
use App\Http\Requests\Admin\UpdateLaporanPenjualanMitraRequest;
use App\Models\DetailLaporanPenjualanMitra;
use App\Models\DetailPengirimanMitra;
use App\Models\DetailProduksiHarian;
use App\Models\HargaProdukMitraBulanan;
use App\Models\LaporanPenjualanMitra;
use App\Models\Mitra;
use App\Models\PengirimanMitra;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanMitraController extends Controller
{
    public function index(Request $request)
    {
        $tanggalInput = trim((string) $request->query('tanggal', ''));
        $mitraId = $request->query('mitra_id');
        try {
            $tanggal = $tanggalInput !== ''
                ? Carbon::parse($tanggalInput)->startOfDay()
                : Carbon::today();
        } catch (\Throwable $e) {
            $tanggal = Carbon::today();
        }
        $tanggalStr = $tanggal->toDateString();

        $laporan = LaporanPenjualanMitra::query()
            ->with(['mitra'])
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->when($mitraId, function ($query) use ($mitraId) {
                $query->where('mitra_id', $mitraId);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $mitra = Mitra::query()->orderBy('nama')->get();

        return view('admin.laporan.index', compact('laporan', 'tanggal', 'tanggalStr', 'mitraId', 'mitra'));
    }

    public function create()
    {
        $mitra = Mitra::query()->orderBy('nama')->get();

        return view('admin.laporan.create', compact('mitra'));
    }

    public function store(StoreLaporanPenjualanMitraRequest $request)
    {
        $laporan = LaporanPenjualanMitra::create($request->validated());

        return redirect('/admin/laporan/'.$laporan->id.'/edit')->with('success', 'Laporan penjualan mitra berhasil dibuat.');
    }

    public function edit(LaporanPenjualanMitra $laporan)
    {
        $laporan->load(['mitra', 'detail.produk']);
        $mitra = Mitra::query()->orderBy('nama')->get();

        // Produk yang ada di pengiriman mitra+tanggal yang sama
        $produkDititip = DetailPengirimanMitra::query()
            ->whereHas('pengiriman', function ($q) use ($laporan) {
                $q->where('mitra_id', $laporan->mitra_id)
                    ->where('tanggal', $laporan->tanggal->toDateString());
            })
            ->pluck('produk_id')
            ->unique()
            ->all();

        $sudahDipakai = $laporan->detail->pluck('produk_id')->all();
        $kandidatIds = array_values(array_diff($produkDititip, $sudahDipakai));

        $produk = Produk::query()
            ->whereIn('id', $kandidatIds ?: [0])
            ->orderBy('nama')
            ->get();

        return view('admin.laporan.edit', compact('laporan', 'mitra', 'produk'));
    }

    public function update(UpdateLaporanPenjualanMitraRequest $request, LaporanPenjualanMitra $laporan)
    {
        $laporan->update($request->validated());

        return redirect('/admin/laporan/'.$laporan->id.'/edit')->with('success', 'Laporan penjualan mitra berhasil diperbarui.');
    }

    public function destroy(LaporanPenjualanMitra $laporan)
    {
        $laporan->delete();

        return redirect('/admin/laporan')->with('success', 'Laporan penjualan mitra berhasil dihapus.');
    }

    public function storeDetail(StoreDetailLaporanPenjualanMitraRequest $request, LaporanPenjualanMitra $laporan)
    {
        $data = $request->validated();
        $data['laporan_penjualan_mitra_id'] = $laporan->id;
        $produkId = (int) $data['produk_id'];

        $harga = HargaProdukMitraBulanan::query()
            ->where('mitra_id', $laporan->mitra_id)
            ->where('produk_id', $produkId)
            ->where('tahun', (int) $laporan->tanggal->format('Y'))
            ->where('bulan', (int) $laporan->tanggal->format('n'))
            ->first();

        if (! $harga) {
            return redirect('/admin/laporan/'.$laporan->id.'/edit')->withErrors([
                'produk_id' => 'Harga bulanan belum diset untuk produk ini pada periode tersebut.',
            ])->withInput();
        }

        $jumlahTitip = $this->resolveJumlahTitip($laporan, $produkId);

        if ($jumlahTitip === null) {
            return redirect('/admin/laporan/'.$laporan->id.'/edit')->withErrors([
                'produk_id' => 'Jumlah titip belum tersedia dari pengiriman mitra pada tanggal tersebut.',
            ])->withInput();
        }

        $stokLayakSumber = $this->resolveStokLayakJualKembali($laporan, $produkId);

        if ($stokLayakSumber === null) {
            return redirect('/admin/laporan/'.$laporan->id.'/edit')->withErrors([
                'produk_id' => 'Status layak jual kembali belum tersedia di produksi harian pada tanggal tersebut.',
            ])->withInput();
        }

        $data['harga_jual'] = $harga->harga_jual;
        $data['margin_per_unit'] = $harga->margin_per_unit;
        $data['jumlah_titip'] = $jumlahTitip;

        $sisaBarang = (int) $data['sisa_barang'];
        $stokLayak = min($stokLayakSumber, $sisaBarang);
        $data['stok_layak_jual_kembali'] = $stokLayak;

        $jumlahTerjual = $jumlahTitip - $sisaBarang;
        $stokTidakLayak = $sisaBarang - $stokLayak;

        $hargaJual = (float) $data['harga_jual'];
        $marginPerUnit = $data['margin_per_unit'] !== null ? (float) $data['margin_per_unit'] : 0.0;

        $data['jumlah_terjual'] = $jumlahTerjual;
        $data['stok_tidak_layak_jual'] = $stokTidakLayak;
        $data['total_penjualan'] = round($jumlahTerjual * $hargaJual, 2);
        $data['total_margin'] = round($jumlahTerjual * $marginPerUnit, 2);

        DetailLaporanPenjualanMitra::create($data);

        return redirect('/admin/laporan/'.$laporan->id.'/edit')->with('success', 'Detail laporan berhasil ditambahkan.');
    }

    public function editDetail(LaporanPenjualanMitra $laporan, DetailLaporanPenjualanMitra $detail)
    {
        if ($detail->laporan_penjualan_mitra_id !== $laporan->id) {
            abort(404);
        }

        $detail->load('produk');
        $produk = Produk::query()->orderBy('nama')->get();

        return view('admin.laporan.detail-edit', compact('laporan', 'detail', 'produk'));
    }

    public function updateDetail(UpdateDetailLaporanPenjualanMitraRequest $request, LaporanPenjualanMitra $laporan, DetailLaporanPenjualanMitra $detail)
    {
        if ($detail->laporan_penjualan_mitra_id !== $laporan->id) {
            abort(404);
        }

        $data = $request->validated();
        $produkId = (int) $detail->produk_id;
        $jumlahTitip = $this->resolveJumlahTitip($laporan, $produkId);

        if ($jumlahTitip === null) {
            return redirect('/admin/laporan/'.$laporan->id.'/detail/'.$detail->id.'/edit')->withErrors([
                'sisa_barang' => 'Jumlah titip belum tersedia dari pengiriman mitra pada tanggal tersebut.',
            ])->withInput();
        }

        $stokLayakSumber = $this->resolveStokLayakJualKembali($laporan, $produkId);

        if ($stokLayakSumber === null) {
            return redirect('/admin/laporan/'.$laporan->id.'/detail/'.$detail->id.'/edit')->withErrors([
                'sisa_barang' => 'Status layak jual kembali belum tersedia di produksi harian pada tanggal tersebut.',
            ])->withInput();
        }

        $sisaBarang = (int) $data['sisa_barang'];
        $stokLayak = min($stokLayakSumber, $sisaBarang);

        $data['jumlah_titip'] = $jumlahTitip;
        $data['stok_layak_jual_kembali'] = $stokLayak;
        $jumlahTerjual = $jumlahTitip - $sisaBarang;
        $stokTidakLayak = $sisaBarang - $stokLayak;

        $hargaJual = (float) $detail->harga_jual;
        $marginPerUnit = $detail->margin_per_unit !== null ? (float) $detail->margin_per_unit : 0.0;

        $data['jumlah_terjual'] = $jumlahTerjual;
        $data['stok_tidak_layak_jual'] = $stokTidakLayak;
        $data['total_penjualan'] = round($jumlahTerjual * $hargaJual, 2);
        $data['total_margin'] = round($jumlahTerjual * $marginPerUnit, 2);

        $detail->update($data);

        return redirect('/admin/laporan/'.$laporan->id.'/edit')->with('success', 'Detail laporan berhasil diperbarui.');
    }

    private function resolveJumlahTitip(LaporanPenjualanMitra $laporan, int $produkId): ?int
    {
        $result = DetailPengirimanMitra::query()
            ->selectRaw('COUNT(*) as row_count, COALESCE(SUM(detail_pengiriman_mitra.jumlah_titip), 0) as jumlah_titip')
            ->where('produk_id', $produkId)
            ->whereHas('pengiriman', function ($query) use ($laporan) {
                $query->whereDate('tanggal', $laporan->tanggal)
                    ->where('mitra_id', $laporan->mitra_id);
            })
            ->first();

        if (! $result || (int) $result->row_count === 0) {
            return null;
        }

        return (int) $result->jumlah_titip;
    }

    private function resolveStokLayakJualKembali(LaporanPenjualanMitra $laporan, int $produkId): ?int
    {
        $detailProduksi = DetailProduksiHarian::query()
            ->where('produk_id', $produkId)
            ->whereHas('produksi', function ($query) use ($laporan) {
                $query->whereDate('tanggal', $laporan->tanggal);
            })
            ->first();

        if (! $detailProduksi) {
            return null;
        }

        return (int) $detailProduksi->stok_layak_jual_kembali;
    }

    public function destroyDetail(LaporanPenjualanMitra $laporan, DetailLaporanPenjualanMitra $detail)
    {
        if ($detail->laporan_penjualan_mitra_id !== $laporan->id) {
            abort(404);
        }

        $detail->delete();

        return redirect('/admin/laporan/'.$laporan->id.'/edit')->with('success', 'Detail laporan berhasil dihapus.');
    }

    public function matriks(Request $request)
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

        // Mitra rows = mitra yang punya pengiriman di tanggal itu
        $pengiriman = PengirimanMitra::query()
            ->with(['mitra', 'detail.produk'])
            ->where('tanggal', $tanggalStr)
            ->get();

        // Kolom produk = union semua produk yang ada di pengiriman tanggal itu
        $produkIds = $pengiriman->flatMap(fn ($p) => $p->detail->pluck('produk_id'))->unique()->all();
        $produk = Produk::query()->whereIn('id', $produkIds)->orderBy('nama')->get();

        // titip[mitra_id][produk_id] = jumlah_titip dari pengiriman
        $titip = [];
        foreach ($pengiriman as $p) {
            foreach ($p->detail as $d) {
                $titip[$p->mitra_id][$d->produk_id] = (int) $d->jumlah_titip;
            }
        }

        // Existing laporan
        $laporan = LaporanPenjualanMitra::query()
            ->with('detail')
            ->where('tanggal', $tanggalStr)
            ->get()
            ->keyBy('mitra_id');

        $existing = [];
        foreach ($laporan as $mitraId => $l) {
            foreach ($l->detail as $d) {
                $existing[$mitraId][$d->produk_id] = [
                    'sisa_barang' => (int) $d->sisa_barang,
                    'jumlah_terjual' => (int) $d->jumlah_terjual,
                ];
            }
        }

        // Stok layak jual kembali per produk (dari detail produksi harian tanggal itu)
        $produksiDetail = DetailProduksiHarian::query()
            ->whereHas('produksi', fn ($q) => $q->where('tanggal', $tanggalStr))
            ->whereIn('produk_id', $produkIds ?: [0])
            ->get()
            ->keyBy('produk_id');
        $stokLayakMap = [];
        foreach ($produksiDetail as $produkId => $d) {
            $stokLayakMap[$produkId] = (int) $d->stok_layak_jual_kembali;
        }

        // Harga map utk periode
        $mitraIds = $pengiriman->pluck('mitra_id')->unique()->all();
        $hargaRows = HargaProdukMitraBulanan::query()
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->whereIn('mitra_id', $mitraIds ?: [0])
            ->whereIn('produk_id', $produkIds ?: [0])
            ->get();
        $hargaMap = [];
        foreach ($hargaRows as $h) {
            $hargaMap[$h->mitra_id][$h->produk_id] = $h;
        }

        // Mitra urut nama
        $mitra = Mitra::query()->whereIn('id', $mitraIds ?: [0])->orderBy('nama')->get();

        return view('admin.laporan.matriks', [
            'tanggal' => $tanggal,
            'tanggalStr' => $tanggalStr,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'mitra' => $mitra,
            'produk' => $produk,
            'titip' => $titip,
            'existing' => $existing,
            'stokLayakMap' => $stokLayakMap,
            'hargaMap' => $hargaMap,
            'laporan' => $laporan,
            'pengirimanCount' => $pengiriman->count(),
        ]);
    }

    public function matriksStore(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'sisa' => ['nullable', 'array'],
            'sisa.*' => ['array'],
            'sisa.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $tanggal = Carbon::parse($data['tanggal'])->startOfDay();
        $tanggalStr = $tanggal->toDateString();
        $tahun = (int) $tanggal->format('Y');
        $bulan = (int) $tanggal->format('n');
        $sisa = $data['sisa'] ?? [];

        $mitraIds = array_keys($sisa);
        $produkIds = collect($sisa)->flatMap(fn ($row) => array_keys($row))->unique()->all();

        // Resolve titip dari pengiriman
        $pengiriman = PengirimanMitra::query()
            ->with('detail')
            ->where('tanggal', $tanggalStr)
            ->whereIn('mitra_id', $mitraIds ?: [0])
            ->get();
        $titip = [];
        foreach ($pengiriman as $p) {
            foreach ($p->detail as $d) {
                $titip[$p->mitra_id][$d->produk_id] = (int) $d->jumlah_titip;
            }
        }

        // Stok layak jual kembali per produk
        $stokLayakMap = [];
        $produksiDetail = DetailProduksiHarian::query()
            ->whereHas('produksi', fn ($q) => $q->where('tanggal', $tanggalStr))
            ->whereIn('produk_id', $produkIds ?: [0])
            ->get();
        foreach ($produksiDetail as $d) {
            $stokLayakMap[$d->produk_id] = (int) $d->stok_layak_jual_kembali;
        }

        // Harga
        $hargaRows = HargaProdukMitraBulanan::query()
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->whereIn('mitra_id', $mitraIds ?: [0])
            ->whereIn('produk_id', $produkIds ?: [0])
            ->get();
        $hargaMap = [];
        foreach ($hargaRows as $h) {
            $hargaMap[$h->mitra_id][$h->produk_id] = $h;
        }

        $errors = [];
        $rowsTouched = 0;

        DB::transaction(function () use ($sisa, $tanggalStr, $titip, $stokLayakMap, $hargaMap, &$errors, &$rowsTouched) {
            foreach ($sisa as $mitraId => $row) {
                $mitraId = (int) $mitraId;
                $row = is_array($row) ? $row : [];

                // Hanya proses produk yang punya jumlah_titip (di pengiriman) untuk mitra ini
                $hasInput = false;
                foreach ($row as $produkId => $value) {
                    if (isset($titip[$mitraId][(int) $produkId])) {
                        $hasInput = true;
                        break;
                    }
                }

                $existingLaporan = LaporanPenjualanMitra::query()
                    ->where('tanggal', $tanggalStr)
                    ->where('mitra_id', $mitraId)
                    ->first();

                if (! $hasInput && ! $existingLaporan) {
                    continue;
                }

                $laporan = $existingLaporan ?: LaporanPenjualanMitra::create([
                    'tanggal' => $tanggalStr,
                    'mitra_id' => $mitraId,
                ]);

                foreach ($row as $produkId => $value) {
                    $produkId = (int) $produkId;
                    $jumlahTitip = $titip[$mitraId][$produkId] ?? null;

                    if ($jumlahTitip === null) {
                        // Tidak ada pengiriman untuk produk ini, skip
                        continue;
                    }

                    $existingDetail = DetailLaporanPenjualanMitra::query()
                        ->where('laporan_penjualan_mitra_id', $laporan->id)
                        ->where('produk_id', $produkId)
                        ->first();

                    if ($value === null || $value === '') {
                        if ($existingDetail) {
                            $existingDetail->delete();
                        }
                        continue;
                    }

                    $sisaBarang = (int) $value;
                    if ($sisaBarang > $jumlahTitip) {
                        $errors[] = "Sisa barang mitra #$mitraId produk #$produkId melebihi jumlah titip ($jumlahTitip).";
                        continue;
                    }

                    $harga = $hargaMap[$mitraId][$produkId] ?? null;
                    if (! $harga) {
                        $errors[] = "Harga bulanan belum diset untuk mitra #$mitraId produk #$produkId.";
                        continue;
                    }

                    if (! isset($stokLayakMap[$produkId])) {
                        $errors[] = "Status layak jual kembali belum tersedia di produksi harian untuk produk #$produkId.";
                        continue;
                    }

                    $stokLayakSumber = (int) $stokLayakMap[$produkId];
                    $stokLayak = min($stokLayakSumber, $sisaBarang);
                    $jumlahTerjual = $jumlahTitip - $sisaBarang;
                    $stokTidakLayak = $sisaBarang - $stokLayak;

                    $hargaJual = (float) $harga->harga_jual;
                    $marginPerUnit = $harga->margin_per_unit !== null ? (float) $harga->margin_per_unit : 0.0;

                    $payload = [
                        'jumlah_titip' => $jumlahTitip,
                        'sisa_barang' => $sisaBarang,
                        'stok_layak_jual_kembali' => $stokLayak,
                        'jumlah_terjual' => $jumlahTerjual,
                        'stok_tidak_layak_jual' => $stokTidakLayak,
                        'harga_jual' => $hargaJual,
                        'margin_per_unit' => $harga->margin_per_unit,
                        'total_penjualan' => round($jumlahTerjual * $hargaJual, 2),
                        'total_margin' => round($jumlahTerjual * $marginPerUnit, 2),
                    ];

                    if ($existingDetail) {
                        $existingDetail->update($payload);
                    } else {
                        DetailLaporanPenjualanMitra::create($payload + [
                            'laporan_penjualan_mitra_id' => $laporan->id,
                            'produk_id' => $produkId,
                        ]);
                    }
                    $rowsTouched++;
                }
            }
        });

        $redirect = redirect('/admin/laporan/matriks?tanggal='.$tanggalStr);
        if ($errors) {
            return $redirect->withErrors($errors);
        }

        return $redirect->with('success', "Matriks laporan tersimpan ($rowsTouched baris detail).");
    }
}
