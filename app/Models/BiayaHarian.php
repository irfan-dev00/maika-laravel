<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaHarian extends Model
{
    protected $table = 'biaya_harian';

    protected $fillable = [
        'tanggal',
        'catatan',
        'total_biaya',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    public function detail()
    {
        return $this->hasMany(DetailBiayaHarian::class, 'biaya_harian_id');
    }
}

