<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // this will be a SuperAdmin...

        User::factory()->create([
            'name' => 'Mehedi Jaman',
            'email' => 'mail4mjaman@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('mehedi!@#'),
            'profile_type' => 'user',
            'profile_id' => 1,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
