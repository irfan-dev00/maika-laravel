<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Buat user owner default
        $owner = User::updateOrCreate(
            ['email' => 'owner@maika.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'), // Ganti dengan password yang aman
            ]
        );

        // Assign role Owner
        $ownerRole = Role::where('nama', 'Owner')->first();
        if ($ownerRole && !$owner->roles->contains($ownerRole->id)) {
            $owner->roles()->attach($ownerRole->id);
        }

        $this->command->info('✓ User owner@maika.com created (password: password)');
    }
}
