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
        Schema::create('tour_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('adult_price', 12, 2)->default(0);
            $table->decimal('child_price', 12, 2)->nullable();
            $table->decimal('infant_price', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tour_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_pricings');
    }
};
