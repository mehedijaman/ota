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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('tour_categories')->nullOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('tour_destinations')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique()->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedSmallInteger('duration_days')->default(1);
            $table->unsignedSmallInteger('duration_nights')->default(0);
            $table->unsignedSmallInteger('min_group_size')->default(1);
            $table->unsignedSmallInteger('max_group_size')->nullable();
            $table->enum('difficulty_level', ['easy', 'moderate', 'challenging', 'extreme'])->default('easy');
            $table->enum('tour_type', ['group', 'private', 'custom'])->default('group');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->decimal('child_price', 12, 2)->nullable();
            $table->decimal('infant_price', 12, 2)->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->enum('deposit_type', ['fixed', 'percentage'])->default('fixed');
            $table->unsignedSmallInteger('cancellation_days')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();
            $table->json('languages')->nullable();
            $table->json('age_restrictions')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_featured']);
            $table->index(['category_id', 'is_active']);
            $table->index(['destination_id', 'is_active']);
            $table->index('base_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
