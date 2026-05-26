<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateDetailLaporanPenjualanMitraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sisa_barang' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $sisaBarang = (int) $this->input('sisa_barang');
            $laporan = $this->route('laporan');
            $detail = $this->route('detail');

            if (! $laporan || ! $detail) {
                return;
            }

            $pengiriman = DB::table('detail_pengiriman_mitra as detail_pengiriman')
                ->join('pengiriman_mitra as pengiriman', 'pengiriman.id', '=', 'detail_pengiriman.pengiriman_mitra_id')
                ->where('pengiriman.mitra_id', $laporan->mitra_id)
                ->whereDate('pengiriman.tanggal', $laporan->tanggal)
                ->where('detail_pengiriman.produk_id', $detail->produk_id)
                ->selectRaw('COUNT(*) as row_count, COALESCE(SUM(detail_pengiriman.jumlah_titip), 0) as jumlah_titip')
                ->first();

            if (! $pengiriman || (int) $pengiriman->row_count === 0) {
                $validator->errors()->add('sisa_barang', 'Jumlah titip belum tersedia dari pengiriman mitra pada tanggal tersebut.');
                return;
            }

            if ($sisaBarang > (int) $pengiriman->jumlah_titip) {
                $validator->errors()->add('sisa_barang', 'Sisa barang tidak boleh lebih besar dari jumlah titip hasil pengiriman mitra.');
            }

            $produksi = DB::table('detail_produksi_harian as detail_produksi')
                ->join('produksi_harian as produksi', 'produksi.id', '=', 'detail_produksi.produksi_harian_id')
                ->whereDate('produksi.tanggal', $laporan->tanggal)
                ->where('detail_produksi.produk_id', $detail->produk_id)
                ->select('detail_produksi.stok_layak_jual_kembali')
                ->first();

            if (! $produksi) {
                $validator->errors()->add('sisa_barang', 'Status layak jual kembali belum tersedia di produksi harian pada tanggal tersebut.');
            }
        });
    }
}
