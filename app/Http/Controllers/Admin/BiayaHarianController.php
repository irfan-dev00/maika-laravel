<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBiayaHarianRequest;
use App\Http\Requests\Admin\StoreDetailBiayaHarianRequest;
use App\Http\Requests\Admin\UpdateBiayaHarianRequest;
use App\Http\Requests\Admin\UpdateDetailBiayaHarianRequest;
use App\Models\BiayaHarian;
use App\Models\DetailBiayaHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiayaHarianController extends Controller
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

        $biaya = BiayaHarian::query()
            ->withCount('detail')
            ->where('tanggal', $tanggalStr)
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('admin.biaya.index', compact('biaya', 'tanggal', 'tanggalStr'));
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
        $prevDate = $tanggal->copy()->subDay()->toDateString();

        $copyFrom = trim((string) $request->query('copy_from', ''));
        $items = [];
        if ($copyFrom !== '') {
            try {
                $copyDate = Carbon::parse($copyFrom)->toDateString();
                $source = BiayaHarian::query()->with('detail')->where('tanggal', $copyDate)->first();
                if ($source) {
                    foreach ($source->detail as $d) {
                        $items[] = [
                            'nama_item' => $d->nama_item,
                            'qty' => $d->qty,
                            'satuan' => $d->satuan,
                            'harga_satuan' => $d->harga_satuan,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // Pastikan ada minimal beberapa baris kosong
        while (count($items) < 5) {
            $items[] = ['nama_item' => '', 'qty' => null, 'satuan' => '', 'harga_satuan' => null];
        }

        return view('admin.biaya.create', [
            'tanggal' => $tanggal,
            'prevDate' => $prevDate,
            'copyFrom' => $copyFrom,
            'items' => $items,
        ]);
    }

    public function store(StoreBiayaHarianRequest $request)
    {
        $data = $request->validated();
        $data['total_biaya'] = 0;
        $detailInput = $request->input('detail', []);

        $biaya = DB::transaction(function () use ($data, $detailInput) {
            $biaya = BiayaHarian::create($data);

            if (is_array($detailInput)) {
                foreach ($detailInput as $row) {
                    $row = is_array($row) ? $row : [];
                    $nama = trim((string) ($row['nama_item'] ?? ''));
                    if ($nama === '') {
                        continue;
                    }
                    $qty = ($row['qty'] !== null && $row['qty'] !== '') ? (float) $row['qty'] : null;
                    $harga = ($row['harga_satuan'] !== null && $row['harga_satuan'] !== '') ? (float) $row['harga_satuan'] : null;
                    $total = ($qty !== null && $harga !== null) ? round($qty * $harga, 2) : 0;

                    DetailBiayaHarian::create([
                        'biaya_harian_id' => $biaya->id,
                        'nama_item' => $nama,
                        'qty' => $qty,
                        'satuan' => $row['satuan'] ?? null,
                        'harga_satuan' => $harga,
                        'total' => $total,
                    ]);
                }
            }

            $this->refreshTotal($biaya->id);

            return $biaya;
        });

        return redirect('/admin/biaya/'.$biaya->id.'/edit')->with('success', 'Biaya harian berhasil dibuat.');
    }

    public function edit(BiayaHarian $biaya)
    {
        $biaya->load('detail');

        return view('admin.biaya.edit', compact('biaya'));
    }

    public function update(UpdateBiayaHarianRequest $request, BiayaHarian $biaya)
    {
        $biaya->update($request->validated());

        return redirect('/admin/biaya/'.$biaya->id.'/edit')->with('success', 'Biaya harian berhasil diperbarui.');
    }

    public function destroy(BiayaHarian $biaya)
    {
        $biaya->delete();

        return redirect('/admin/biaya')->with('success', 'Biaya harian berhasil dihapus.');
    }

    public function storeDetail(StoreDetailBiayaHarianRequest $request, BiayaHarian $biaya)
    {
        $data = $request->validated();
        $data['biaya_harian_id'] = $biaya->id;

        $qty = $data['qty'] ?? null;
        $harga = $data['harga_satuan'] ?? null;
        $data['total'] = ($qty !== null && $harga !== null) ? round(((float) $qty) * ((float) $harga), 2) : 0;

        DetailBiayaHarian::create($data);
        $this->refreshTotal($biaya->id);

        return redirect('/admin/biaya/'.$biaya->id.'/edit')->with('success', 'Detail biaya berhasil ditambahkan.');
    }

    public function editDetail(BiayaHarian $biaya, DetailBiayaHarian $detail)
    {
        if ($detail->biaya_harian_id !== $biaya->id) {
            abort(404);
        }

        return view('admin.biaya.detail-edit', compact('biaya', 'detail'));
    }

    public function updateDetail(UpdateDetailBiayaHarianRequest $request, BiayaHarian $biaya, DetailBiayaHarian $detail)
    {
        if ($detail->biaya_harian_id !== $biaya->id) {
            abort(404);
        }

        $data = $request->validated();
        $qty = $data['qty'] ?? null;
        $harga = $data['harga_satuan'] ?? null;
        $data['total'] = ($qty !== null && $harga !== null) ? round(((float) $qty) * ((float) $harga), 2) : 0;

        $detail->update($data);
        $this->refreshTotal($biaya->id);

        return redirect('/admin/biaya/'.$biaya->id.'/edit')->with('success', 'Detail biaya berhasil diperbarui.');
    }

    public function destroyDetail(BiayaHarian $biaya, DetailBiayaHarian $detail)
    {
        if ($detail->biaya_harian_id !== $biaya->id) {
            abort(404);
        }

        $detail->delete();
        $this->refreshTotal($biaya->id);

        return redirect('/admin/biaya/'.$biaya->id.'/edit')->with('success', 'Detail biaya berhasil dihapus.');
    }

    private function refreshTotal(int $biayaId): void
    {
        $sum = (float) DB::table('detail_biaya_harian')->where('biaya_harian_id', $biayaId)->sum('total');
        DB::table('biaya_harian')->where('id', $biayaId)->update(['total_biaya' => round($sum, 2), 'updated_at' => now()]);
    }
}

