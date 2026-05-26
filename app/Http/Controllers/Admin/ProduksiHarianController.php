<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDetailProduksiHarianRequest;
use App\Http\Requests\Admin\StoreProduksiHarianRequest;
use App\Http\Requests\Admin\UpdateDetailProduksiHarianRequest;
use App\Http\Requests\Admin\UpdateProduksiHarianRequest;
use App\Models\DetailLaporanPenjualanMitra;
use App\Models\DetailProduksiHarian;
use App\Models\Produk;
use App\Models\ProduksiHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiHarianController extends Controller
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

        $produksi = ProduksiHarian::query()
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('admin.produksi.index', compact('produksi', 'tanggal', 'tanggalStr'));
    }

    public function create(Request $request)
    {
        $tanggalInput = trim((string) $request->query('tanggal', ''));
        try {
            $tanggal = $tanggalInput !== ''
                ? Carbon::parse($tanggalInput)->startOfDay()
                : Carbon::today();
        } catch (\Throwable $e) {
            $tanggal = Carbon::today();
        }

        $copyFrom = trim((string) $request->query('copy_from', ''));
        $copyFromDate = null;
        if ($copyFrom !== '') {
            try {
                $copyFromDate = Carbon::parse($copyFrom)->startOfDay();
            } catch (\Throwable $e) {
                $copyFromDate = null;
            }
        }

        $produk = Produk::query()->where('is_aktif', true)->orderBy('nama')->get();

        // Prefill stok_awal = SUM(sisa laporan penjualan mitra hari sebelumnya)
        // Stok awal = barang yang dikembalikan mitra karena tidak terjual.
        $prevDate = $tanggal->copy()->subDay()->toDateString();
        $prevSisa = DetailLaporanPenjualanMitra::query()
            ->whereHas('laporan', fn ($q) => $q->where('tanggal', $prevDate))
            ->selectRaw('produk_id, SUM(sisa) as total_sisa')
            ->groupBy('produk_id')
            ->pluck('total_sisa', 'produk_id')
            ->toArray();

        // Sumber copy: produksi pada tanggal copy_from
        $sourceProduksi = null;
        if ($copyFromDate) {
            $sourceProduksi = ProduksiHarian::query()->with('detail')->where('tanggal', $copyFromDate->toDateString())->first();
        }

        $prefill = [];
        foreach ($produk as $p) {
            // stok_awal = total sisa laporan mitra kemarin
            $stokAwal = (int) ($prevSisa[$p->id] ?? 0);
            $jumlahProduksi = 0;
            $stokLayak = 0;
            if ($sourceProduksi) {
                $row = $sourceProduksi->detail->firstWhere('produk_id', $p->id);
                if ($row) {
                    $jumlahProduksi = (int) $row->jumlah_produksi;
                    // stok_layak tidak di-copy karena bergantung kondisi fisik hari ini
                }
            }
            $prefill[$p->id] = [
                'stok_awal' => $stokAwal,
                'jumlah_produksi' => $jumlahProduksi,
                'stok_layak_jual_kembali' => $stokLayak,
            ];
        }

        return view('admin.produksi.create', [
            'tanggal' => $tanggal,
            'produk' => $produk,
            'prefill' => $prefill,
            'prevDate' => $prevDate,
            'copyFrom' => $copyFromDate ? $copyFromDate->toDateString() : '',
        ]);
    }

    public function store(StoreProduksiHarianRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $detailInput = $request->input('detail', []);

        $produksi = DB::transaction(function () use ($data, $detailInput) {
            $produksi = ProduksiHarian::create($data);

            if (is_array($detailInput)) {
                foreach ($detailInput as $produkId => $row) {
                    $produkId = (int) $produkId;
                    $row = is_array($row) ? $row : [];
                    $stokAwal = (int) ($row['stok_awal'] ?? 0);
                    $jumlahProduksi = (int) ($row['jumlah_produksi'] ?? 0);
                    $stokLayak = (int) ($row['stok_layak_jual_kembali'] ?? 0);

                    if ($stokAwal === 0 && $jumlahProduksi === 0 && $stokLayak === 0) {
                        continue;
                    }

                    DetailProduksiHarian::create([
                        'produksi_harian_id' => $produksi->id,
                        'produk_id' => $produkId,
                        'stok_awal' => $stokAwal,
                        'jumlah_produksi' => $jumlahProduksi,
                        'stok_layak_jual_kembali' => $stokLayak,
                        'stok_siap_jual' => $jumlahProduksi + $stokLayak,
                    ]);
                }
            }

            return $produksi;
        });

        return redirect('/admin/produksi/'.$produksi->id.'/edit')->with('success', 'Produksi harian berhasil dibuat.');
    }

    public function edit(ProduksiHarian $produksi)
    {
        $produksi->load(['detail.produk']);
        $produk = Produk::query()->orderBy('nama')->get();

        return view('admin.produksi.edit', compact('produksi', 'produk'));
    }

    public function update(UpdateProduksiHarianRequest $request, ProduksiHarian $produksi)
    {
        $produksi->update($request->validated());

        return redirect('/admin/produksi/'.$produksi->id.'/edit')->with('success', 'Produksi harian berhasil diperbarui.');
    }

    public function destroy(ProduksiHarian $produksi)
    {
        $produksi->delete();

        return redirect('/admin/produksi')->with('success', 'Produksi harian berhasil dihapus.');
    }

    public function storeDetail(StoreDetailProduksiHarianRequest $request, ProduksiHarian $produksi)
    {
        $data = $request->validated();
        $data['produksi_harian_id'] = $produksi->id;
        $data['stok_siap_jual'] = (int) $data['jumlah_produksi'] + (int) $data['stok_layak_jual_kembali'];

        DetailProduksiHarian::create($data);

        return redirect('/admin/produksi/'.$produksi->id.'/edit')->with('success', 'Detail produksi berhasil ditambahkan.');
    }

    public function editDetail(ProduksiHarian $produksi, DetailProduksiHarian $detail)
    {
        if ($detail->produksi_harian_id !== $produksi->id) {
            abort(404);
        }

        $detail->load('produk');

        return view('admin.produksi.detail-edit', compact('produksi', 'detail'));
    }

    public function updateDetail(UpdateDetailProduksiHarianRequest $request, ProduksiHarian $produksi, DetailProduksiHarian $detail)
    {
        if ($detail->produksi_harian_id !== $produksi->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['stok_siap_jual'] = (int) $data['jumlah_produksi'] + (int) $data['stok_layak_jual_kembali'];

        $detail->update($data);

        return redirect('/admin/produksi/'.$produksi->id.'/edit')->with('success', 'Detail produksi berhasil diperbarui.');
    }

    public function destroyDetail(ProduksiHarian $produksi, DetailProduksiHarian $detail)
    {
        if ($detail->produksi_harian_id !== $produksi->id) {
            abort(404);
        }

        $detail->delete();

        return redirect('/admin/produksi/'.$produksi->id.'/edit')->with('success', 'Detail produksi berhasil dihapus.');
    }
}

