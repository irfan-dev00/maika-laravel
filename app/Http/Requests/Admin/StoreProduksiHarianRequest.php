<?php

namespace App\Http\Requests\Admin;

use App\Services\KalenderOperasionalGate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreProduksiHarianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal' => ['required', 'date', 'unique:produksi_harian,tanggal'],
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
