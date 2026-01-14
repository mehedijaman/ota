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
        Schema::create('hotel_room_amenity_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->foreignId('room_amenity_id')->constrained('hotel_room_amenities')->cascadeOnDelete();
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'room_amenity_id']);
            $table->index('room_amenity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_amenity_room');
    }
};
