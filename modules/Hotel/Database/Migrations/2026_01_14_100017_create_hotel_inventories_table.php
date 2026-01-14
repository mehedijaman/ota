<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotel_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->date('date');

            // Inventory Counts
            $table->unsignedSmallInteger('total_inventory')->default(0)->comment('Total physical rooms');
            $table->unsignedSmallInteger('booked')->default(0)->comment('Currently booked');
            $table->unsignedSmallInteger('blocked')->default(0)->comment('Manually blocked/maintenance');
            $table->unsignedSmallInteger('held')->default(0)->comment('Temporary hold for booking in progress');
            $table->unsignedSmallInteger('available')->default(0)->comment('Calculated available');

            // Overbooking
            $table->unsignedTinyInteger('oversell_limit')->default(0)->comment('Allow overbooking by X rooms');

            // Sync tracking
            $table->timestamp('last_sync_at')->nullable()->comment('Last supplier sync');
            $table->string('sync_source')->nullable()->comment('manual, api, scheduler');

            $table->timestamps();

            // Unique constraint
            $table->unique(['room_id', 'date']);

            // Indexes for availability searches
            $table->index(['date', 'available']);
            $table->index(['room_id', 'date', 'available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_inventories');
    }
};
