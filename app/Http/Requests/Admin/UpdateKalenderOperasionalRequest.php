<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKalenderOperasionalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $kalenderId = optional($this->route('kalender'))->id;

        return [
            'tanggal' => ['required', 'date', Rule::unique('kalender_operasional', 'tanggal')->ignore($kalenderId)],
            'status' => ['required', 'in:operasional,libur'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}

