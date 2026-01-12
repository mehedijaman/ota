<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Employees with linked Users...');

        progress(
            label: 'Employees creation status',
            steps: 10,
            callback: function () {
                $employee = Employee::factory()->create();

                User::factory()->create([
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'password' => Hash::make('password'),
                    'profile_type' => 'employee',
                    'profile_id' => $employee->id,
                ]);

                return $employee;
            },
        );

        info('10 Employees with linked Users created.');

        Schema::enableForeignKeyConstraints();
    }
}
