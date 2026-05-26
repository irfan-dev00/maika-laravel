<?php

namespace App\Http\Requests\Admin;

use App\Services\KalenderOperasionalGate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateLaporanPenjualanMitraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tanggal' => ['required', 'date'],
            'mitra_id' => ['required', 'integer', 'exists:mitra,id'],
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

            $laporanId = optional($this->route('laporan'))->id;
            $tanggal = (string) $this->input('tanggal');
            $mitraId = $this->input('mitra_id');

            if ($laporanId && $tanggal && $mitraId) {
                $exists = DB::table('laporan_penjualan_mitra')
                    ->where('tanggal', $tanggal)
                    ->where('mitra_id', $mitraId)
                    ->where('id', '<>', $laporanId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('mitra_id', 'Laporan untuk mitra dan tanggal tersebut sudah ada.');
                }
            }
        });
    }
}

