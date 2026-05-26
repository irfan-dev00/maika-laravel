<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaProdukMitraBulanan extends Model
{
    protected $table = 'harga_produk_mitra_bulanan';

    protected $fillable = [
        'mitra_id',
        'produk_id',
        'tahun',
        'bulan',
        'harga_jual',
        'margin_per_unit',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'harga_jual' => 'decimal:2',
        'margin_per_unit' => 'decimal:2',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}

