<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHargaProdukMitraBulananRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $hargaId = optional($this->route('harga'))->id;

        return [
            'mitra_id' => ['required', 'integer', 'exists:mitra,id'],
            'produk_id' => ['required', 'integer', 'exists:produk,id'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'margin_per_unit' => ['nullable', 'numeric', 'min:0'],
            'unique_check' => [
                function ($attribute, $value, $fail) use ($hargaId) {
                    $mitraId = $this->input('mitra_id');
                    $produkId = $this->input('produk_id');
                    $tahun = $this->input('tahun');
                    $bulan = $this->input('bulan');

                    if (! $mitraId || ! $produkId || ! $tahun || ! $bulan) {
                        return;
                    }

                    $exists = \DB::table('harga_produk_mitra_bulanan')
                        ->where('mitra_id', $mitraId)
                        ->where('produk_id', $produkId)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('id', '<>', $hargaId)
                        ->exists();

                    if ($exists) {
                        $fail('Harga untuk kombinasi mitra, produk, tahun, dan bulan tersebut sudah ada.');
                    }
                },
            ],
        ];
    }
}

