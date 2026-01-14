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
        Schema::create('hotel_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable()->comment('Icon class or name');
            $table->enum('category', [
                'general',
                'room',
                'bathroom',
                'food_drink',
                'pool_beach',
                'wellness_spa',
                'business',
                'parking',
                'internet',
                'entertainment',
                'family',
                'accessibility',
                'services',
                'safety',
                'transportation',
            ])->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_popular')->default(false)->comment('Show in quick filters');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['is_popular', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_amenities');
    }
};
