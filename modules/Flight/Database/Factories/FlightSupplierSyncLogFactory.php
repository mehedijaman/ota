<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightSupplier;
use Modules\Flight\Models\FlightSupplierSyncLog;

class FlightSupplierSyncLogFactory extends Factory
{
    protected $model = FlightSupplierSyncLog::class;

    public function definition(): array
    {
        $created = $this->faker->numberBetween(100, 1000);
        $updated = $this->faker->numberBetween(50, 500);
        $failed = $this->faker->numberBetween(0, 20);

        return [
            'supplier_id' => FlightSupplier::factory(),
            'type' => $this->faker->randomElement(['schedule_import', 'fare_update', 'inventory_sync']),
            'status' => 'completed',
            'total_records' => $created + $updated + $failed,
            'created_count' => $created,
            'updated_count' => $updated,
            'skipped_count' => 0,
            'failed_count' => $failed,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
            'duration_seconds' => $this->faker->numberBetween(60, 600),
        ];
    }
}
