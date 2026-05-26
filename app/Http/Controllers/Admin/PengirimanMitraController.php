<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDetailPengirimanMitraRequest;
use App\Http\Requests\Admin\StorePengirimanMitraRequest;
use App\Http\Requests\Admin\UpdateDetailPengirimanMitraRequest;
use App\Http\Requests\Admin\UpdatePengirimanMitraRequest;
use App\Models\DetailPengirimanMitra;
use App\Models\HargaProdukMitraBulanan;
use App\Models\Mitra;
use App\Models\PengirimanMitra;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanMitraController extends Controller
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

        $pengiriman = PengirimanMitra::query()
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

        return view('admin.pengiriman.index', compact('pengiriman', 'tanggal', 'tanggalStr', 'mitraId', 'mitra'));
    }

    public function create()
    {
        $mitra = Mitra::query()->orderBy('nama')->get();

        return view('admin.pengiriman.create', compact('mitra'));
    }

    public function store(StorePengirimanMitraRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $pengiriman = PengirimanMitra::create($data);

        return redirect('/admin/pengiriman/'.$pengiriman->id.'/edit')->with('success', 'Pengiriman mitra berhasil dibuat.');
    }

    public function edit(PengirimanMitra $pengiriman)
    {
        $pengiriman->load(['mitra', 'detail.produk']);
        $mitra = Mitra::query()->orderBy('nama')->get();

        $tahun = (int) $pengiriman->tanggal->format('Y');
        $bulan = (int) $pengiriman->tanggal->format('n');

        // Produk dengan harga bulanan untuk mitra+periode
        $produkBerharga = HargaProdukMitraBulanan::query()
            ->where('mitra_id', $pengiriman->mitra_id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->pluck('produk_id')
            ->all();

        $sudahDipakai = $pengiriman->detail->pluck('produk_id')->all();
        $kandidatIds = array_values(array_diff($produkBerharga, $sudahDipakai));

        $produk = Produk::query()
            ->whereIn('id', $kandidatIds ?: [0])
            ->orderBy('nama')
            ->get();

        $produkTanpaHarga = Produk::query()
            ->where('is_aktif', true)
            ->whereNotIn('id', $produkBerharga ?: [0])
            ->orderBy('nama')
            ->get();

        return view('admin.pengiriman.edit', compact('pengiriman', 'mitra', 'produk', 'produkTanpaHarga', 'tahun', 'bulan'));
    }

    public function update(UpdatePengirimanMitraRequest $request, PengirimanMitra $pengiriman)
    {
        $pengiriman->update($request->validated());

        return redirect('/admin/pengiriman/'.$pengiriman->id.'/edit')->with('success', 'Pengiriman mitra berhasil diperbarui.');
    }

    public function destroy(PengirimanMitra $pengiriman)
    {
        $pengiriman->delete();

        return redirect('/admin/pengiriman')->with('success', 'Pengiriman mitra berhasil dihapus.');
    }

    public function storeDetail(StoreDetailPengirimanMitraRequest $request, PengirimanMitra $pengiriman)
    {
        $data = $request->validated();
        $data['pengiriman_mitra_id'] = $pengiriman->id;

        $harga = HargaProdukMitraBulanan::query()
            ->where('mitra_id', $pengiriman->mitra_id)
            ->where('produk_id', $data['produk_id'])
            ->where('tahun', (int) $pengiriman->tanggal->format('Y'))
            ->where('bulan', (int) $pengiriman->tanggal->format('n'))
            ->first();

        if (! $harga) {
            return redirect('/admin/pengiriman/'.$pengiriman->id.'/edit')->withErrors([
                'produk_id' => 'Harga bulanan belum diset untuk produk ini pada periode tersebut.',
            ])->withInput();
        }

        $data['harga_jual'] = $harga->harga_jual;
        $data['margin_per_unit'] = $harga->margin_per_unit;

        DetailPengirimanMitra::create($data);

        return redirect('/admin/pengiriman/'.$pengiriman->id.'/edit')->with('success', 'Detail pengiriman berhasil ditambahkan.');
    }

    public function editDetail(PengirimanMitra $pengiriman, DetailPengirimanMitra $detail)
    {
        if ($detail->pengiriman_mitra_id !== $pengiriman->id) {
            abort(404);
        }

        $detail->load('produk');
        $produk = Produk::query()->orderBy('nama')->get();

        return view('admin.pengiriman.detail-edit', compact('pengiriman', 'detail', 'produk'));
    }

    public function updateDetail(UpdateDetailPengirimanMitraRequest $request, PengirimanMitra $pengiriman, DetailPengirimanMitra $detail)
    {
        if ($detail->pengiriman_mitra_id !== $pengiriman->id) {
            abort(404);
        }

        $detail->update($request->validated());

        return redirect('/admin/pengiriman/'.$pengiriman->id.'/edit')->with('success', 'Detail pengiriman berhasil diperbarui.');
    }

    public function destroyDetail(PengirimanMitra $pengiriman, DetailPengirimanMitra $detail)
    {
        if ($detail->pengiriman_mitra_id !== $pengiriman->id) {
            abort(404);
        }

        $detail->delete();

        return redirect('/admin/pengiriman/'.$pengiriman->id.'/edit')->with('success', 'Detail pengiriman berhasil dihapus.');
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

        $mitra = Mitra::query()->where('is_aktif', true)->orderBy('nama')->get();
        $produk = Produk::query()->where('is_aktif', true)->orderBy('nama')->get();

        $harga = HargaProdukMitraBulanan::query()
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->whereIn('mitra_id', $mitra->pluck('id'))
            ->whereIn('produk_id', $produk->pluck('id'))
            ->get();
        $hargaMap = [];
        foreach ($harga as $h) {
            $hargaMap[$h->mitra_id][$h->produk_id] = $h;
        }

        // Tampilkan kolom hanya produk yang punya harga utk minimal 1 mitra di periode itu
        $produkAktifIds = collect($hargaMap)->flatMap(fn ($byProduk) => array_keys($byProduk))->unique()->all();
        $produk = $produk->whereIn('id', $produkAktifIds)->values();

        $pengiriman = PengirimanMitra::query()
            ->with('detail')
            ->where('tanggal', $tanggalStr)
            ->get()
            ->keyBy('mitra_id');

        // existing[mitra_id][produk_id] = jumlah_titip
        $existing = [];
        foreach ($pengiriman as $mitraId => $p) {
            foreach ($p->detail as $d) {
                $existing[$mitraId][$d->produk_id] = (int) $d->jumlah_titip;
            }
        }

        return view('admin.pengiriman.matriks', [
            'tanggal' => $tanggal,
            'tanggalStr' => $tanggalStr,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'mitra' => $mitra,
            'produk' => $produk,
            'hargaMap' => $hargaMap,
            'existing' => $existing,
            'pengiriman' => $pengiriman,
        ]);
    }

    public function matriksStore(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'qty' => ['nullable', 'array'],
            'qty.*' => ['array'],
            'qty.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $tanggal = Carbon::parse($data['tanggal'])->startOfDay();
        $tanggalStr = $tanggal->toDateString();
        $tahun = (int) $tanggal->format('Y');
        $bulan = (int) $tanggal->format('n');
        $qty = $data['qty'] ?? [];

        // Harga map
        $mitraIds = array_keys($qty);
        $produkIds = collect($qty)->flatMap(fn ($row) => array_keys($row))->unique()->all();

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
        $createdOrUpdated = 0;

        DB::transaction(function () use ($qty, $tanggal, $tanggalStr, $hargaMap, &$createdOrUpdated, &$errors) {
            foreach ($qty as $mitraId => $row) {
                $mitraId = (int) $mitraId;
                $row = is_array($row) ? $row : [];

                // Skip mitra bila semua kosong & belum ada pengiriman
                $hasInput = collect($row)->contains(fn ($v) => $v !== null && $v !== '' && (int) $v > 0);
                $existingPengiriman = PengirimanMitra::query()
                    ->where('tanggal', $tanggalStr)
                    ->where('mitra_id', $mitraId)
                    ->first();

                if (! $hasInput && ! $existingPengiriman) {
                    continue;
                }

                $pengiriman = $existingPengiriman ?: PengirimanMitra::create([
                    'tanggal' => $tanggalStr,
                    'mitra_id' => $mitraId,
                    'created_by' => auth()->id(),
                ]);

                foreach ($row as $produkId => $value) {
                    $produkId = (int) $produkId;
                    $value = ($value === null || $value === '') ? 0 : (int) $value;

                    $existingDetail = DetailPengirimanMitra::query()
                        ->where('pengiriman_mitra_id', $pengiriman->id)
                        ->where('produk_id', $produkId)
                        ->first();

                    if ($value <= 0) {
                        if ($existingDetail) {
                            $existingDetail->delete();
                        }
                        continue;
                    }

                    $harga = $hargaMap[$mitraId][$produkId] ?? null;
                    if (! $harga) {
                        $errors[] = "Harga bulanan belum diset untuk mitra #$mitraId produk #$produkId.";
                        continue;
                    }

                    if ($existingDetail) {
                        $existingDetail->update([
                            'jumlah_titip' => $value,
                            'harga_jual' => $harga->harga_jual,
                            'margin_per_unit' => $harga->margin_per_unit,
                        ]);
                    } else {
                        DetailPengirimanMitra::create([
                            'pengiriman_mitra_id' => $pengiriman->id,
                            'produk_id' => $produkId,
                            'jumlah_titip' => $value,
                            'harga_jual' => $harga->harga_jual,
                            'margin_per_unit' => $harga->margin_per_unit,
                        ]);
                    }
                    $createdOrUpdated++;
                }
            }
        });

        $redirect = redirect('/admin/pengiriman/matriks?tanggal='.$tanggalStr);
        if ($errors) {
            return $redirect->withErrors($errors);
        }

        return $redirect->with('success', "Matriks pengiriman tersimpan ($createdOrUpdated baris detail).");
    }

    public function harian(Request $request)
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
        $prevDate = $tanggal->copy()->subDay()->toDateString();
        $tahun = (int) $tanggal->format('Y');
        $bulan = (int) $tanggal->format('n');

        $copyFromInput = trim((string) $request->query('copy_from', ''));
        $copyFromDate = null;
        if ($copyFromInput !== '') {
            try {
                $copyFromDate = Carbon::parse($copyFromInput)->startOfDay();
            } catch (\Throwable $e) {
                $copyFromDate = null;
            }
        }

        $mitra = Mitra::query()->where('is_aktif', true)->orderBy('nama')->get();
        $produk = Produk::query()->where('is_aktif', true)->orderBy('nama')->get();

        // hargaMap[mitra_id][produk_id] = ['harga_jual' => ..., 'margin_per_unit' => ...]
        $hargaRows = HargaProdukMitraBulanan::query()
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->whereIn('mitra_id', $mitra->pluck('id') ?: [0])
            ->get();
        $hargaMap = [];
        foreach ($hargaRows as $h) {
            $hargaMap[$h->mitra_id][$h->produk_id] = [
                'harga_jual' => (float) $h->harga_jual,
                'margin_per_unit' => $h->margin_per_unit !== null ? (float) $h->margin_per_unit : null,
            ];
        }

        // Existing pengiriman pada tanggal ini
        $existingPengiriman = PengirimanMitra::query()
            ->with(['detail.produk'])
            ->where('tanggal', $tanggalStr)
            ->get();

        $sections = [];
        $usedMitraIds = [];
        foreach ($existingPengiriman as $p) {
            $rows = [];
            foreach ($p->detail as $d) {
                $rows[] = [
                    'produk_id' => $d->produk_id,
                    'jumlah_titip' => (int) $d->jumlah_titip,
                    'harga_jual' => (float) $d->harga_jual,
                ];
            }
            $sections[] = [
                'mitra_id' => $p->mitra_id,
                'rows' => $rows,
                'is_existing' => true,
            ];
            $usedMitraIds[] = $p->mitra_id;
        }

        // Copy-from-previous: tambahkan mitra dari tanggal sumber yg belum ada
        $copyInfo = null;
        if ($copyFromDate) {
            $copyStr = $copyFromDate->toDateString();
            $sourcePengiriman = PengirimanMitra::query()
                ->with(['detail'])
                ->where('tanggal', $copyStr)
                ->get();
            $copiedCount = 0;
            foreach ($sourcePengiriman as $sp) {
                if (in_array($sp->mitra_id, $usedMitraIds, true)) {
                    continue;
                }
                $rows = [];
                foreach ($sp->detail as $d) {
                    if (! isset($hargaMap[$sp->mitra_id][$d->produk_id])) {
                        continue;
                    }
                    $rows[] = [
                        'produk_id' => $d->produk_id,
                        'jumlah_titip' => (int) $d->jumlah_titip,
                        'harga_jual' => $hargaMap[$sp->mitra_id][$d->produk_id]['harga_jual'],
                    ];
                }
                if (empty($rows)) {
                    continue;
                }
                $sections[] = [
                    'mitra_id' => $sp->mitra_id,
                    'rows' => $rows,
                    'is_existing' => false,
                ];
                $usedMitraIds[] = $sp->mitra_id;
                $copiedCount++;
            }
            $copyInfo = ['from' => $copyStr, 'count' => $copiedCount];
        }

        $availableMitra = $mitra->reject(fn ($m) => in_array($m->id, $usedMitraIds, true))->values();

        return view('admin.pengiriman.harian', [
            'tanggal' => $tanggal,
            'tanggalStr' => $tanggalStr,
            'prevDate' => $prevDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'mitra' => $mitra,
            'produk' => $produk,
            'hargaMap' => $hargaMap,
            'sections' => $sections,
            'availableMitra' => $availableMitra,
            'copyInfo' => $copyInfo,
        ]);
    }

    public function harianStore(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'mitras' => ['nullable', 'array'],
            'mitras.*.rows' => ['nullable', 'array'],
            'mitras.*.rows.*.produk_id' => ['nullable', 'integer'],
            'mitras.*.rows.*.jumlah_titip' => ['nullable', 'integer', 'min:0'],
        ]);

        $tanggal = Carbon::parse($data['tanggal'])->startOfDay();
        $tanggalStr = $tanggal->toDateString();
        $tahun = (int) $tanggal->format('Y');
        $bulan = (int) $tanggal->format('n');
        $mitrasInput = $data['mitras'] ?? [];

        // [mitra_id => [produk_id => jumlah]]
        $clean = [];
        foreach ($mitrasInput as $mitraId => $payload) {
            $mitraId = (int) $mitraId;
            if ($mitraId <= 0) {
                continue;
            }
            $rows = $payload['rows'] ?? [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $produkId = (int) ($row['produk_id'] ?? 0);
                $jumlah = (int) ($row['jumlah_titip'] ?? 0);
                if ($produkId <= 0 || $jumlah <= 0) {
                    continue;
                }
                $clean[$mitraId][$produkId] = ($clean[$mitraId][$produkId] ?? 0) + $jumlah;
            }
        }

        $mitraIds = array_keys($clean);
        $produkIds = collect($clean)->flatMap(fn ($r) => array_keys($r))->unique()->all();

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

        $missing = [];
        foreach ($clean as $mitraId => $produkRows) {
            foreach ($produkRows as $produkId => $jumlah) {
                if (! isset($hargaMap[$mitraId][$produkId])) {
                    $missing[] = "mitra #$mitraId / produk #$produkId";
                }
            }
        }
        if ($missing) {
            return back()->withInput()->withErrors([
                'harga' => 'Harga bulanan belum diset untuk: '.implode(', ', $missing),
            ]);
        }

        $stats = ['created' => 0, 'updated' => 0, 'deleted_headers' => 0, 'deleted_details' => 0];

        DB::transaction(function () use ($clean, $tanggalStr, $hargaMap, &$stats) {
            $existingHeaders = PengirimanMitra::query()
                ->with('detail')
                ->where('tanggal', $tanggalStr)
                ->get()
                ->keyBy('mitra_id');

            foreach ($existingHeaders as $mitraId => $header) {
                if (! array_key_exists($mitraId, $clean)) {
                    $header->detail()->delete();
                    $header->delete();
                    $stats['deleted_headers']++;
                }
            }

            foreach ($clean as $mitraId => $produkRows) {
                $header = $existingHeaders->get($mitraId) ?: PengirimanMitra::create([
                    'tanggal' => $tanggalStr,
                    'mitra_id' => $mitraId,
                    'created_by' => auth()->id(),
                ]);

                $existingDetails = DetailPengirimanMitra::query()
                    ->where('pengiriman_mitra_id', $header->id)
                    ->get()
                    ->keyBy('produk_id');

                foreach ($existingDetails as $produkId => $det) {
                    if (! array_key_exists($produkId, $produkRows)) {
                        $det->delete();
                        $stats['deleted_details']++;
                    }
                }

                foreach ($produkRows as $produkId => $jumlah) {
                    $harga = $hargaMap[$mitraId][$produkId];
                    $det = $existingDetails->get($produkId);
                    if ($det) {
                        $det->update([
                            'jumlah_titip' => $jumlah,
                            'harga_jual' => $harga->harga_jual,
                            'margin_per_unit' => $harga->margin_per_unit,
                        ]);
                        $stats['updated']++;
                    } else {
                        DetailPengirimanMitra::create([
                            'pengiriman_mitra_id' => $header->id,
                            'produk_id' => $produkId,
                            'jumlah_titip' => $jumlah,
                            'harga_jual' => $harga->harga_jual,
                            'margin_per_unit' => $harga->margin_per_unit,
                        ]);
                        $stats['created']++;
                    }
                }
            }
        });

        $msg = "Tersimpan: {$stats['created']} baru, {$stats['updated']} update";
        if ($stats['deleted_headers'] > 0 || $stats['deleted_details'] > 0) {
            $msg .= ", {$stats['deleted_headers']} mitra dihapus, {$stats['deleted_details']} detail dihapus";
        }
        $msg .= '.';

        return redirect('/admin/pengiriman/harian?tanggal='.$tanggalStr)->with('success', $msg);
    }
}
