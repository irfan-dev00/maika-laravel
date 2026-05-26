<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KalenderOperasionalGate
{
    public function assertOperasional(string $tanggal): void
    {
        $row = DB::table('kalender_operasional')->where('tanggal', $tanggal)->first();

        if (! $row) {
            throw ValidationException::withMessages([
                'tanggal' => ['Tanggal belum terdaftar di kalender operasional.'],
            ]);
        }

        if ($row->status !== 'operasional') {
            throw ValidationException::withMessages([
                'tanggal' => ['Tanggal berstatus libur, transaksi tidak diperbolehkan.'],
            ]);
        }
    }
}

