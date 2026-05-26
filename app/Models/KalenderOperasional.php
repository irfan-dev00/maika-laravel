<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderOperasional extends Model
{
    protected $table = 'kalender_operasional';

    protected $fillable = [
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}

