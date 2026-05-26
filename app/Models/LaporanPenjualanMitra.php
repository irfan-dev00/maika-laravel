<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPenjualanMitra extends Model
{
    protected $table = 'laporan_penjualan_mitra';

    protected $fillable = [
        'tanggal',
        'mitra_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailLaporanPenjualanMitra::class, 'laporan_penjualan_mitra_id');
    }
}

