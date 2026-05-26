<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreDetailLaporanPenjualanMitraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'produk_id' => ['required', 'integer', 'exists:produk,id'],
            'sisa_barang' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $sisaBarang = (int) $this->input('sisa_barang');

            $laporan = $this->route('laporan');
            $laporanId = optional($laporan)->id;
            $produkId = $this->input('produk_id');
            if ($laporanId && $produkId) {
                $exists = DB::table('detail_laporan_penjualan_mitra')
                    ->where('laporan_penjualan_mitra_id', $laporanId)
                    ->where('produk_id', $produkId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('produk_id', 'Produk ini sudah ada di detail laporan.');
                }
            }

            if ($laporan && $produkId) {
                $pengiriman = DB::table('detail_pengiriman_mitra as detail')
                    ->join('pengiriman_mitra as pengiriman', 'pengiriman.id', '=', 'detail.pengiriman_mitra_id')
                    ->where('pengiriman.mitra_id', $laporan->mitra_id)
                    ->whereDate('pengiriman.tanggal', $laporan->tanggal)
                    ->where('detail.produk_id', $produkId)
                    ->selectRaw('COUNT(*) as row_count, COALESCE(SUM(detail.jumlah_titip), 0) as jumlah_titip')
                    ->first();

                if (! $pengiriman || (int) $pengiriman->row_count === 0) {
                    $validator->errors()->add('produk_id', 'Jumlah titip belum tersedia dari pengiriman mitra pada tanggal tersebut.');
                } elseif ($sisaBarang > (int) $pengiriman->jumlah_titip) {
                    $validator->errors()->add('sisa_barang', 'Sisa barang tidak boleh lebih besar dari jumlah titip hasil pengiriman mitra.');
                }

                $produksi = DB::table('detail_produksi_harian as detail')
                    ->join('produksi_harian as produksi', 'produksi.id', '=', 'detail.produksi_harian_id')
                    ->whereDate('produksi.tanggal', $laporan->tanggal)
                    ->where('detail.produk_id', $produkId)
                    ->select('detail.stok_layak_jual_kembali')
                    ->first();

                if (! $produksi) {
                    $validator->errors()->add('produk_id', 'Status layak jual kembali belum tersedia di produksi harian pada tanggal tersebut.');
                }

                $tahun = (int) $laporan->tanggal->format('Y');
                $bulan = (int) $laporan->tanggal->format('n');

                $harga = DB::table('harga_produk_mitra_bulanan')
                    ->where('mitra_id', $laporan->mitra_id)
                    ->where('produk_id', $produkId)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();

                if (! $harga) {
                    $validator->errors()->add('produk_id', 'Harga bulanan belum diset untuk produk ini pada periode tersebut.');
                }
            }
        });
    }
}
