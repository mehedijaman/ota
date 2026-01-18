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
        Schema::create('flight_faqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('airline_id')->nullable()->constrained('flight_airlines')->cascadeOnDelete();

            // FAQ Content
            $table->string('question');
            $table->text('answer');

            // Categorization
            $table->enum('category', [
                'general',
                'booking',
                'payment',
                'baggage',
                'checkin',
                'cancellation',
                'refund',
                'exchange',
                'visa',
                'special_assistance',
                'infants_children',
                'pets',
                'loyalty_program',
                'covid',
                'other',
            ])->default('general');

            // Display
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Analytics
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['airline_id', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['is_active', 'sort_order']);
            // Note: Add fullText index for MySQL: $table->fullText(['question', 'answer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_faqs');
    }
};
