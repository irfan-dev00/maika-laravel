<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKalenderOperasionalRequest;
use App\Http\Requests\Admin\UpdateKalenderOperasionalRequest;
use App\Models\KalenderOperasional;
use Carbon\Carbon;
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

        return view('admin.kalender.index', compact('kalender', 'bulan', 'tahun'));
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

