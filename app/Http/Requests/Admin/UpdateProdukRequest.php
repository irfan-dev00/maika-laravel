<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProdukRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $produkId = optional($this->route('produk'))->id;

        return [
            'kode_produk' => ['required', 'string', 'regex:/^[A-Z]{3}[0-9]{3}$/', Rule::unique('produk', 'kode_produk')->ignore($produkId)],
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:255'],
            'harga_modal_per_unit' => ['nullable', 'numeric', 'min:0'],
            'is_aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'kode_produk.regex' => 'Kode produk harus 3 huruf kapital diikuti 3 angka, contoh: BAK002.',
            'kode_produk.unique' => 'Kode produk sudah digunakan.',
        ];
    }
}

