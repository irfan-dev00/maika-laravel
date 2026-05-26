<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengirimanMitra extends Model
{
    protected $table = 'detail_pengiriman_mitra';

    protected $fillable = [
        'pengiriman_mitra_id',
        'produk_id',
        'jumlah_titip',
        'harga_jual',
        'margin_per_unit',
    ];

    protected $casts = [
        'jumlah_titip' => 'integer',
        'harga_jual' => 'decimal:2',
        'margin_per_unit' => 'decimal:2',
    ];

    public function pengiriman()
    {
        return $this->belongsTo(PengirimanMitra::class, 'pengiriman_mitra_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}

