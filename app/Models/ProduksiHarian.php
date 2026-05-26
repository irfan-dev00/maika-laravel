<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiHarian extends Model
{
    protected $table = 'produksi_harian';

    protected $fillable = [
        'tanggal',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function detail()
    {
        return $this->hasMany(DetailProduksiHarian::class, 'produksi_harian_id');
    }
}

