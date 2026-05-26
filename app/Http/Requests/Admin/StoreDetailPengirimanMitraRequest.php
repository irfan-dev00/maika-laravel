<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreDetailPengirimanMitraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'produk_id' => ['required', 'integer', 'exists:produk,id'],
            'jumlah_titip' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $pengiriman = $this->route('pengiriman');
            $pengirimanId = optional($pengiriman)->id;
            $produkId = $this->input('produk_id');

            if ($pengirimanId && $produkId) {
                $exists = DB::table('detail_pengiriman_mitra')
                    ->where('pengiriman_mitra_id', $pengirimanId)
                    ->where('produk_id', $produkId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('produk_id', 'Produk ini sudah ada di detail pengiriman.');
                }
            }

            if ($pengiriman && $produkId) {
                $tahun = (int) $pengiriman->tanggal->format('Y');
                $bulan = (int) $pengiriman->tanggal->format('n');

                $harga = DB::table('harga_produk_mitra_bulanan')
                    ->where('mitra_id', $pengiriman->mitra_id)
                    ->where('produk_id', $produkId)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();

                if (! $harga) {
                    $validator->errors()->add('produk_id', 'Harga bulanan belum diset untuk produk ini pada periode tersebut.');
                }
            }
        });
    }
}
