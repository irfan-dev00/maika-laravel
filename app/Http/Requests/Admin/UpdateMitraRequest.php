<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMitraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $mitraId = optional($this->route('mitra'))->id;

        return [
            'kode_mitra' => ['required', 'string', 'regex:/^[A-Z]{3}[0-9]{3}$/', Rule::unique('mitra', 'kode_mitra')->ignore($mitraId)],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'is_aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'kode_mitra.regex' => 'Kode mitra harus 3 huruf kapital diikuti 3 angka, contoh: BAK002.',
            'kode_mitra.unique' => 'Kode mitra sudah digunakan.',
        ];
    }
}

