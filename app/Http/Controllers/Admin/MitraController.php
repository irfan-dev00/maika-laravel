<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMitraRequest;
use App\Http\Requests\Admin\UpdateMitraRequest;
use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $mitra = Mitra::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('kode_mitra', 'like', '%'.$q.'%')
                        ->orWhere('nama', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.mitra.index', compact('mitra', 'q'));
    }

    public function create()
    {
        $lastKode = Mitra::query()
            ->whereRaw("kode_mitra REGEXP '^[A-Z]{3}[0-9]{3}$'")
            ->orderByRaw('CAST(SUBSTR(kode_mitra, 4) AS UNSIGNED) DESC')
            ->value('kode_mitra');

        $nextNum = $lastKode ? ((int) substr($lastKode, 3) + 1) : 1;
        $nextNumber = str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.mitra.create', compact('nextNumber'));
    }

    public function store(StoreMitraRequest $request)
    {
        $data = $request->validated();
        $data['is_aktif'] = (bool) ($data['is_aktif'] ?? false);

        Mitra::create($data);

        return redirect('/admin/mitra')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit(Mitra $mitra)
    {
        return view('admin.mitra.edit', compact('mitra'));
    }

    public function update(UpdateMitraRequest $request, Mitra $mitra)
    {
        $data = $request->validated();
        $data['is_aktif'] = (bool) ($data['is_aktif'] ?? false);

        $mitra->update($data);

        return redirect('/admin/mitra')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy(Mitra $mitra)
    {
        $mitra->delete();

        return redirect('/admin/mitra')->with('success', 'Mitra berhasil dihapus.');
    }
}

