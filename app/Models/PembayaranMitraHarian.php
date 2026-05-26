<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranMitraHarian extends Model
{
    protected $table = 'pembayaran_mitra_harian';

    protected $fillable = [
        'tanggal',
        'mitra_id',
        'laporan_penjualan_mitra_id',
        'jumlah_bayar',
        'metode',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function laporan()
    {
        return $this->belongsTo(LaporanPenjualanMitra::class, 'laporan_penjualan_mitra_id');
    }
}

