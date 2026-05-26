<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHargaProdukMitraBulananRequest;
use App\Http\Requests\Admin\UpdateHargaProdukMitraBulananRequest;
use App\Models\HargaProdukMitraBulanan;
use App\Models\Mitra;
use App\Models\Produk;
use Illuminate\Http\Request;

class HargaProdukMitraBulananController extends Controller
{
    public function index(Request $request)
    {
        $mitraId = $request->query('mitra_id');
        $tahun = $request->query('tahun');
        $bulan = $request->query('bulan');

        $harga = HargaProdukMitraBulanan::query()
            ->with(['mitra', 'produk'])
            ->when($mitraId, function ($query) use ($mitraId) {
                $query->where('mitra_id', $mitraId);
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            })
            ->when($bulan, function ($query) use ($bulan) {
                $query->where('bulan', $bulan);
            })
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderBy('mitra_id')
            ->orderBy('produk_id')
            ->paginate(15)
            ->withQueryString();

        $mitra = Mitra::query()->orderBy('nama')->get();

        return view('admin.harga.index', compact('harga', 'mitra', 'mitraId', 'tahun', 'bulan'));
    }

    public function create()
    {
        $mitra = Mitra::query()->orderBy('nama')->get();
        $produk = Produk::query()->orderBy('nama')->get();

        // Map existing harga: "mitra_id:tahun:bulan" => [produk_id, ...]
        $hargaMap = [];
        HargaProdukMitraBulanan::query()
            ->select(['mitra_id', 'produk_id', 'tahun', 'bulan'])
            ->get()
            ->each(function ($h) use (&$hargaMap) {
                $hargaMap["{$h->mitra_id}:{$h->tahun}:{$h->bulan}"][] = (int) $h->produk_id;
            });

        return view('admin.harga.create', compact('mitra', 'produk', 'hargaMap'));
    }

    public function store(StoreHargaProdukMitraBulananRequest $request)
    {
        $data = $request->validated();
        unset($data['unique_check']);

        HargaProdukMitraBulanan::create($data);

        return redirect('/admin/harga')->with('success', 'Harga bulanan berhasil ditambahkan.');
    }

    public function edit(HargaProdukMitraBulanan $harga)
    {
        $mitra = Mitra::query()->orderBy('nama')->get();
        $produk = Produk::query()->orderBy('nama')->get();

        return view('admin.harga.edit', compact('harga', 'mitra', 'produk'));
    }

    public function update(UpdateHargaProdukMitraBulananRequest $request, HargaProdukMitraBulanan $harga)
    {
        $data = $request->validated();
        unset($data['unique_check']);

        $harga->update($data);

        return redirect('/admin/harga')->with('success', 'Harga bulanan berhasil diperbarui.');
    }

    public function destroy(HargaProdukMitraBulanan $harga)
    {
        $harga->delete();

        return redirect('/admin/harga')->with('success', 'Harga bulanan berhasil dihapus.');
    }
}

