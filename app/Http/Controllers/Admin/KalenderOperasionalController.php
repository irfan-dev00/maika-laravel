<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKalenderOperasionalRequest;
use App\Http\Requests\Admin\UpdateKalenderOperasionalRequest;
use App\Models\KalenderOperasional;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class KalenderOperasionalController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->query('bulan', (int) date('n'));
        $tahun = (int) $request->query('tahun', (int) date('Y'));

        $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $kalender = KalenderOperasional::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $totalHari = $start->daysInMonth;
        $totalTerisi = $kalender->count();
        $totalOperasional = $kalender->where('status', 'operasional')->count();
        $totalLibur = $kalender->where('status', 'libur')->count();
        $totalKurang = max(0, $totalHari - $totalTerisi);
        $statusBulan = $totalTerisi === 0
            ? 'kosong'
            : ($totalTerisi < $totalHari ? 'belum_lengkap' : 'lengkap');

        $ringkasanBulan = compact(
            'totalHari',
            'totalTerisi',
            'totalOperasional',
            'totalLibur',
            'totalKurang',
            'statusBulan'
        );

        return view('admin.kalender.index', compact('kalender', 'bulan', 'tahun', 'ringkasanBulan'));
    }

    public function create()
    {
        return view('admin.kalender.create');
    }

    public function store(StoreKalenderOperasionalRequest $request)
    {
        KalenderOperasional::create($request->validated());

        return redirect('/admin/kalender')->with('success', 'Kalender operasional berhasil ditambahkan.');
    }

    public function generateBulan(Request $request)
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];

        $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();
        $inserted = 0;

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $row = KalenderOperasional::firstOrCreate(
                ['tanggal' => $date->toDateString()],
                ['status' => 'operasional', 'keterangan' => null]
            );

            if ($row->wasRecentlyCreated) {
                $inserted++;
            }
        }

        $message = $inserted > 0
            ? "Generate kalender berhasil. {$inserted} tanggal baru ditambahkan."
            : 'Kalender bulan ini sudah lengkap. Tidak ada tanggal baru yang ditambahkan.';

        return redirect("/admin/kalender?bulan={$bulan}&tahun={$tahun}")->with('success', $message);
    }

    public function edit(KalenderOperasional $kalender)
    {
        return view('admin.kalender.edit', compact('kalender'));
    }

    public function update(UpdateKalenderOperasionalRequest $request, KalenderOperasional $kalender)
    {
        $kalender->update($request->validated());

        $bulan = $request->input('_bulan');
        $tahun = $request->input('_tahun');
        $qs    = ($bulan && $tahun) ? "?bulan={$bulan}&tahun={$tahun}" : '';

        return redirect('/admin/kalender' . $qs)->with('success', 'Kalender operasional berhasil diperbarui.');
    }

    public function destroy(KalenderOperasional $kalender)
    {
        $kalender->delete();

        return redirect('/admin/kalender')->with('success', 'Kalender operasional berhasil dihapus.');
    }
}
