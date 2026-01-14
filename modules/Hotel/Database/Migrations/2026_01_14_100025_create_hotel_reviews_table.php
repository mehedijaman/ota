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
        Schema::create('hotel_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('hotel_bookings')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();

            // Ratings (1-5 scale)
            $table->unsignedTinyInteger('overall_rating');
            $table->unsignedTinyInteger('cleanliness_rating')->nullable();
            $table->unsignedTinyInteger('service_rating')->nullable();
            $table->unsignedTinyInteger('location_rating')->nullable();
            $table->unsignedTinyInteger('value_rating')->nullable();
            $table->unsignedTinyInteger('facilities_rating')->nullable();
            $table->unsignedTinyInteger('comfort_rating')->nullable();
            $table->unsignedTinyInteger('food_rating')->nullable();

            // Review Content
            $table->string('title')->nullable();
            $table->text('content');
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();

            // Reviewer Details
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_country')->nullable();
            $table->enum('travel_type', [
                'business',
                'leisure',
                'family',
                'couple',
                'solo',
                'group',
                'friends',
            ])->nullable();
            $table->date('stayed_in')->nullable()->comment('Month/year of stay');
            $table->unsignedTinyInteger('nights_stayed')->nullable();
            $table->string('room_type_stayed')->nullable();

            // Status
            $table->boolean('is_verified')->default(false)->comment('Verified booking');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_anonymous')->default(false);

            // Engagement
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->unsignedInteger('reported_count')->default(0);

            // Hotel Response
            $table->text('hotel_response')->nullable();
            $table->timestamp('hotel_responded_at')->nullable();
            $table->foreignId('hotel_responded_by')->nullable()->constrained('users')->nullOnDelete();

            // Moderation
            $table->enum('moderation_status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending');
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['hotel_id', 'is_published']);
            $table->index(['hotel_id', 'overall_rating']);
            $table->index(['customer_id']);
            $table->index(['booking_id']);
            $table->index('moderation_status');
            $table->index('is_featured');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_reviews');
    }
};
