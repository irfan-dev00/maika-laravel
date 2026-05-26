<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLaporanPenjualanMitra extends Model
{
    protected $table = 'detail_laporan_penjualan_mitra';

    protected $fillable = [
        'laporan_penjualan_mitra_id',
        'produk_id',
        'jumlah_titip',
        'sisa_barang',
        'stok_layak_jual_kembali',
        'jumlah_terjual',
        'stok_tidak_layak_jual',
        'harga_jual',
        'margin_per_unit',
        'total_penjualan',
        'total_margin',
    ];

    protected $casts = [
        'jumlah_titip' => 'integer',
        'sisa_barang' => 'integer',
        'stok_layak_jual_kembali' => 'integer',
        'jumlah_terjual' => 'integer',
        'stok_tidak_layak_jual' => 'integer',
        'harga_jual' => 'decimal:2',
        'margin_per_unit' => 'decimal:2',
        'total_penjualan' => 'decimal:2',
        'total_margin' => 'decimal:2',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanPenjualanMitra::class, 'laporan_penjualan_mitra_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}

