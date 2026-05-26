<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['nama' => 'Owner'],
            ['nama' => 'Admin'],
        ];

        foreach ($roles as $role) {
            DB::table('role')->updateOrInsert(
                ['nama' => $role['nama']],
                ['nama' => $role['nama'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}

