<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProdukRequest;
use App\Http\Requests\Admin\UpdateProdukRequest;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $produk = Produk::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('kode_produk', 'like', '%'.$q.'%')
                        ->orWhere('nama', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.produk.index', compact('produk', 'q'));
    }

    private function getSatuanList()
    {
        return [
            'Pcs' => 'Pcs (Pieces)',
            'Bks' => 'Bks (Bungkus)',
            'Box' => 'Box (Kardus)',
            'Ltr' => 'Ltr (Liter)',
            'Kg' => 'Kg (Kilogram)',
            'Gr' => 'Gr (Gram)',
            'Ikat' => 'Ikat',
            'Dus' => 'Dus (Lusin)',
            'Botol' => 'Botol',
            'Kaleng' => 'Kaleng',
            'Paket' => 'Paket',
            'Porsi' => 'Porsi',
        ];
    }

    public function create()
    {
        // Hitung nomor urut berikutnya dari kode format [A-Z]{3}[0-9]{3}
        $lastKode = Produk::query()
            ->whereRaw("kode_produk REGEXP '^[A-Z]{3}[0-9]{3}$'")
            ->orderByRaw('CAST(SUBSTR(kode_produk, 4) AS UNSIGNED) DESC')
            ->value('kode_produk');

        $nextNum = $lastKode ? ((int) substr($lastKode, 3) + 1) : 1;
        $nextNumber = str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        $satuan = $this->getSatuanList();

        return view('admin.produk.create', compact('nextNumber', 'satuan'));
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();
        $data['is_aktif'] = (bool) ($data['is_aktif'] ?? false);

        Produk::create($data);

        return redirect('/admin/produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $satuan = $this->getSatuanList();
        return view('admin.produk.edit', compact('produk', 'satuan'));
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $data = $request->validated();
        $data['is_aktif'] = (bool) ($data['is_aktif'] ?? false);

        $produk->update($data);

        return redirect('/admin/produk')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect('/admin/produk')->with('success', 'Produk berhasil dihapus.');
    }
}

