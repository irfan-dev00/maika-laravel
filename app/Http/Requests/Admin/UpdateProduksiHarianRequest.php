<?php

namespace App\Http\Requests\Admin;

use App\Services\KalenderOperasionalGate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateProduksiHarianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $produksiId = optional($this->route('produksi'))->id;

        return [
            'tanggal' => ['required', 'date', Rule::unique('produksi_harian', 'tanggal')->ignore($produksiId)],
            'catatan' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                app(KalenderOperasionalGate::class)->assertOperasional((string) $this->input('tanggal'));
            } catch (ValidationException $e) {
                $validator->errors()->add('tanggal', $e->errors()['tanggal'][0] ?? 'Tanggal tidak valid.');
            }
        });
    }
}

