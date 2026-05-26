<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanMitra extends Model
{
    protected $table = 'pengiriman_mitra';

    protected $fillable = [
        'tanggal',
        'mitra_id',
        'catatan',
        'created_by',
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
        return $this->hasMany(DetailPengirimanMitra::class, 'pengiriman_mitra_id');
    }
}

