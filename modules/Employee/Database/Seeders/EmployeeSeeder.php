<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Employee\Models\Employee;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Employees...');
        $this->seedWithProgress(
            label: 'Employees creation status',
            steps: 5,
            callback: fn () => Employee::factory()->create(),
        );
        info('Employees created.');

        Schema::enableForeignKeyConstraints();
    }

    private function seedWithProgress(string $label, int $steps, callable $callback): void
    {
        progress(
            label: $label,
            steps: $steps,
            callback: $callback,
        );
    }
}
