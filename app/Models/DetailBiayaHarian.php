<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBiayaHarian extends Model
{
    protected $table = 'detail_biaya_harian';

    protected $fillable = [
        'biaya_harian_id',
        'nama_item',
        'qty',
        'satuan',
        'harga_satuan',
        'total',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function biaya()
    {
        return $this->belongsTo(BiayaHarian::class, 'biaya_harian_id');
    }
}

