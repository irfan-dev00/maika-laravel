<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePembayaranMitraHarianRequest;
use App\Http\Requests\Admin\UpdatePembayaranMitraHarianRequest;
use App\Models\LaporanPenjualanMitra;
use App\Models\Mitra;
use App\Models\PembayaranMitraHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembayaranMitraHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggalInput = trim((string) $request->query('tanggal', ''));
        $mitraId = $request->query('mitra_id');
        $status = $request->query('status');
        try {
            $tanggal = $tanggalInput !== ''
                ? Carbon::parse($tanggalInput)->startOfDay()
                : Carbon::today();
        } catch (\Throwable $e) {
            $tanggal = Carbon::today();
        }
        $tanggalStr = $tanggal->toDateString();

        $pembayaran = PembayaranMitraHarian::query()
            ->with(['mitra', 'laporan'])
            ->where('tanggal', $tanggalStr)
            ->when($mitraId, function ($query) use ($mitraId) {
                $query->where('mitra_id', $mitraId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $mitra = Mitra::query()->orderBy('nama')->get();

        return view('admin.pembayaran.index', compact('pembayaran', 'mitra', 'tanggal', 'tanggalStr', 'mitraId', 'status'));
    }

    public function create(Request $request)
    {
        $mitra = Mitra::query()->orderBy('nama')->get();
        $laporan = LaporanPenjualanMitra::query()
            ->with('mitra')
            ->whereNotIn('id', function ($q) {
                $q->select('laporan_penjualan_mitra_id')->from('pembayaran_mitra_harian');
            })
            ->orderByDesc('tanggal')
            ->limit(200)
            ->get();

        $prefill = [
            'tanggal' => '',
            'mitra_id' => '',
            'laporan_penjualan_mitra_id' => '',
            'jumlah_bayar' => '',
        ];

        $laporanId = $request->query('laporan_id');
        if ($laporanId) {
            $sumber = LaporanPenjualanMitra::query()->with('detail')->find($laporanId);
            if ($sumber) {
                $prefill['tanggal'] = $sumber->tanggal ? $sumber->tanggal->toDateString() : '';
                $prefill['mitra_id'] = $sumber->mitra_id;
                $prefill['laporan_penjualan_mitra_id'] = $sumber->id;
                $prefill['jumlah_bayar'] = round($sumber->detail->sum('total_penjualan'), 2);

                // Pastikan laporan sumber muncul di dropdown walau sudah punya pembayaran (untuk konteks)
                if (! $laporan->contains('id', $sumber->id)) {
                    $laporan->prepend($sumber);
                }
            }
        }

        return view('admin.pembayaran.create', compact('mitra', 'laporan', 'prefill'));
    }

    public function store(StorePembayaranMitraHarianRequest $request)
    {
        PembayaranMitraHarian::create($request->validated());

        return redirect('/admin/pembayaran')->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function edit(PembayaranMitraHarian $pembayaran)
    {
        $mitra = Mitra::query()->orderBy('nama')->get();
        $laporan = LaporanPenjualanMitra::query()
            ->with('mitra')
            ->where(function ($q) use ($pembayaran) {
                $q->whereNotIn('id', function ($sub) use ($pembayaran) {
                    $sub->select('laporan_penjualan_mitra_id')
                        ->from('pembayaran_mitra_harian')
                        ->where('id', '<>', $pembayaran->id);
                })->orWhere('id', $pembayaran->laporan_penjualan_mitra_id);
            })
            ->orderByDesc('tanggal')
            ->limit(200)
            ->get();

        return view('admin.pembayaran.edit', compact('pembayaran', 'mitra', 'laporan'));
    }

    public function update(UpdatePembayaranMitraHarianRequest $request, PembayaranMitraHarian $pembayaran)
    {
        $pembayaran->update($request->validated());

        return redirect('/admin/pembayaran')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(PembayaranMitraHarian $pembayaran)
    {
        $pembayaran->delete();

        return redirect('/admin/pembayaran')->with('success', 'Pembayaran berhasil dihapus.');
    }
}

