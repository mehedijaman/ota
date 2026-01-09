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
            $table->decimal('price_per_person', 12, 2)->default(0);
            $table->decimal('price_per_couple', 12, 2)->nullable();
            $table->decimal('price_per_group', 12, 2)->nullable();
            $table->unsignedSmallInteger('group_size_min')->nullable();
            $table->unsignedSmallInteger('group_size_max')->nullable();
            $table->decimal('child_price', 12, 2)->nullable();
            $table->decimal('infant_price', 12, 2)->nullable();
            $table->decimal('single_supplement', 12, 2)->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tour_id', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
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
