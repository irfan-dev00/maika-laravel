<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKalenderOperasionalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal' => ['required', 'date', 'unique:kalender_operasional,tanggal'],
            'status' => ['required', 'in:operasional,libur'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}

