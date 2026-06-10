<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetOwnerPasswordSeeder extends Seeder
{
    public function run()
    {
        $email = 'owner@maika.com';
        $temporaryPassword = 'maika12345';

        $owner = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Owner',
                'password' => Hash::make($temporaryPassword),
                'email_verified_at' => now(),
            ]
        );

        $ownerRole = Role::firstOrCreate(['nama' => 'Owner']);
        $owner->roles()->syncWithoutDetaching([$ownerRole->id]);

        $this->command->info("Owner account ready: {$email}");
        $this->command->info("Temporary password: {$temporaryPassword}");
    }
}
