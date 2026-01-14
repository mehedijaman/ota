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
        Schema::create('hotel_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('hotel_cities')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('radius_km', 5, 2)->nullable()->comment('Coverage radius in km');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hotels_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamps();

            $table->unique(['city_id', 'slug']);
            $table->index(['city_id', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_areas');
    }
};
