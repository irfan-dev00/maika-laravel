<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduksiHarian extends Model
{
    protected $table = 'detail_produksi_harian';

    protected $fillable = [
        'produksi_harian_id',
        'produk_id',
        'stok_awal',
        'jumlah_produksi',
        'stok_layak_jual_kembali',
        'stok_siap_jual',
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'jumlah_produksi' => 'integer',
        'stok_layak_jual_kembali' => 'integer',
        'stok_siap_jual' => 'integer',
    ];

    public function produksi()
    {
        return $this->belongsTo(ProduksiHarian::class, 'produksi_harian_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}

