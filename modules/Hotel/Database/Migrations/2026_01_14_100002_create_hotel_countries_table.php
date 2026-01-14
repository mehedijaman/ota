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
        Schema::create('hotel_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique()->comment('ISO 3166-1 alpha-3');
            $table->string('code_alpha2', 2)->unique()->comment('ISO 3166-1 alpha-2');
            $table->string('currency_code', 3)->nullable()->comment('Default currency');
            $table->string('phone_code', 10)->nullable();
            $table->string('capital')->nullable();
            $table->string('region')->nullable()->comment('Continent/region');
            $table->string('subregion')->nullable();
            $table->string('flag_emoji', 10)->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hotels_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_popular']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_countries');
    }
};
