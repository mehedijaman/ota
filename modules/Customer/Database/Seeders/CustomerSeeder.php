<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Customer\Models\Customer;
use Modules\User\Models\User;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Customers with linked Users...');

        progress(
            label: 'Customers creation status',
            steps: 10,
            callback: function () {
                $customer = Customer::factory()->active()->create();

                User::factory()->create([
                    'name' => $customer->full_name,
                    'email' => $customer->email,
                    'password' => Hash::make('password'),
                    'profile_type' => 'customer',
                    'profile_id' => $customer->id,
                ]);

                return $customer;
            },
        );

        info('10 Customers with linked Users created.');

        Schema::enableForeignKeyConstraints();
    }
}
