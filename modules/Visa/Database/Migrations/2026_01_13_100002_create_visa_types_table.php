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
        Schema::create('visa_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('visa_countries')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('visa_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('code', 50)->nullable()->comment('Official visa type code');
            $table->text('description')->nullable();
            $table->enum('entry_type', ['single', 'double', 'multiple'])->default('single');
            $table->unsignedSmallInteger('validity_days')->nullable()->comment('Visa validity in days');
            $table->unsignedSmallInteger('stay_days')->nullable()->comment('Maximum stay duration in days');
            $table->unsignedSmallInteger('extension_days')->nullable()->comment('Possible extension in days');
            $table->boolean('is_extendable')->default(false);
            $table->boolean('is_convertible')->default(false)->comment('Can be converted to another visa type');
            $table->boolean('work_permitted')->default(false);
            $table->boolean('study_permitted')->default(false);
            $table->unsignedSmallInteger('min_age')->nullable();
            $table->unsignedSmallInteger('max_age')->nullable();
            $table->json('eligible_nationalities')->nullable()->comment('Array of eligible country codes, null means all');
            $table->json('restricted_nationalities')->nullable()->comment('Array of restricted country codes');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['country_id', 'slug']);
            $table->index(['country_id', 'category_id', 'is_active']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_types');
    }
};
