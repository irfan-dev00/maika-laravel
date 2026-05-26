<?php

namespace App\Http\Requests\Admin;

use App\Services\KalenderOperasionalGate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdatePembayaranMitraHarianRequest extends FormRequest
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
            'laporan_penjualan_mitra_id' => ['required', 'integer', 'exists:laporan_penjualan_mitra,id'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'metode' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,confirmed'],
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

            $pembayaranId = optional($this->route('pembayaran'))->id;
            $laporanId = $this->input('laporan_penjualan_mitra_id');
            $mitraId = $this->input('mitra_id');
            $tanggal = (string) $this->input('tanggal');

            if ($laporanId && $mitraId) {
                $laporan = DB::table('laporan_penjualan_mitra')->where('id', $laporanId)->first();
                if ($laporan && (int) $laporan->mitra_id !== (int) $mitraId) {
                    $validator->errors()->add('laporan_penjualan_mitra_id', 'Laporan tidak sesuai dengan mitra yang dipilih.');
                }
                if ($laporan && $tanggal && (string) $laporan->tanggal !== $tanggal) {
                    $validator->errors()->add('laporan_penjualan_mitra_id', 'Laporan tidak sesuai dengan tanggal yang dipilih.');
                }
            }

            if ($laporanId) {
                $exists = DB::table('pembayaran_mitra_harian')
                    ->where('laporan_penjualan_mitra_id', $laporanId)
                    ->where('id', '<>', $pembayaranId)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('laporan_penjualan_mitra_id', 'Pembayaran untuk laporan tersebut sudah ada.');
                }
            }
        });
    }
}

