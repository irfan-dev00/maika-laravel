<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreDetailProduksiHarianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'produk_id' => ['required', 'integer', 'exists:produk,id'],
            'stok_awal' => ['required', 'integer', 'min:0'],
            'jumlah_produksi' => ['required', 'integer', 'min:0'],
            'stok_layak_jual_kembali' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $stokAwal = (int) $this->input('stok_awal', 0);
            $stokLayak = (int) $this->input('stok_layak_jual_kembali', 0);
            if ($stokLayak > $stokAwal) {
                $validator->errors()->add('stok_layak_jual_kembali', 'Layak jual kembali ('.$stokLayak.') tidak boleh melebihi stok awal ('.$stokAwal.'). Stok awal adalah total barang yang kembali dari mitra.');
            }

            $produksiId = optional($this->route('produksi'))->id;
            $produkId = $this->input('produk_id');

            if ($produksiId && $produkId) {
                $exists = DB::table('detail_produksi_harian')
                    ->where('produksi_harian_id', $produksiId)
                    ->where('produk_id', $produkId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('produk_id', 'Produk ini sudah ada di detail produksi.');
                }
            }
        });
    }
}
