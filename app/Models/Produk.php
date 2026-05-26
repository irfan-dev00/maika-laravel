<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'kode_produk',
        'nama',
        'satuan',
        'harga_modal_per_unit',
        'is_aktif',
    ];

    protected $casts = [
        'harga_modal_per_unit' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];
}

