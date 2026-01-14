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
        Schema::create('hotel_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('hotel_countries')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('state_province')->nullable();
            $table->string('state_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone')->nullable();
            $table->string('iata_code', 3)->nullable()->comment('Airport code');
            $table->text('description')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hotels_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'is_active']);
            $table->index(['is_active', 'is_popular']);
            $table->index(['latitude', 'longitude']);
            $table->index('iata_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_cities');
    }
};
