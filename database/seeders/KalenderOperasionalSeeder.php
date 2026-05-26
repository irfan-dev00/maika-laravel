<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KalenderOperasionalSeeder extends Seeder
{
    public function run()
    {
        $startYear = (int) date('Y');
        $endYear = $startYear + 1;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $end = Carbon::createFromDate($year, 12, 31)->startOfDay();

            foreach (CarbonPeriod::create($start, $end) as $date) {
                $tanggal = $date->format('Y-m-d');
                DB::table('kalender_operasional')->updateOrInsert(
                    ['tanggal' => $tanggal],
                    ['status' => 'operasional', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}

