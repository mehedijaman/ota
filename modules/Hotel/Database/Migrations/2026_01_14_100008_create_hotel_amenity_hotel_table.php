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
        Schema::create('hotel_amenity_hotel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('hotel_amenities')->cascadeOnDelete();
            $table->boolean('is_free')->default(true);
            $table->decimal('fee', 10, 2)->nullable()->comment('Fee if not free');
            $table->enum('fee_type', ['per_stay', 'per_night', 'per_person', 'per_use'])->nullable();
            $table->string('notes')->nullable()->comment('Additional info');
            $table->timestamps();

            $table->unique(['hotel_id', 'amenity_id']);
            $table->index('amenity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_amenity_hotel');
    }
};
