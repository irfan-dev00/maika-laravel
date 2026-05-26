<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetailBiayaHarianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_item' => ['required', 'string', 'max:255'],
            'qty' => ['nullable', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:255'],
            'harga_satuan' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

