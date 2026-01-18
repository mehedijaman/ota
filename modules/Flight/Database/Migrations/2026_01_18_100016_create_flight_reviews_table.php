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
        Schema::create('flight_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->nullable()->constrained('flight_bookings')->nullOnDelete();
            $table->foreignId('airline_id')->nullable()->constrained('flight_airlines')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();

            // Ratings (1-5 scale)
            $table->unsignedTinyInteger('overall_rating');
            $table->unsignedTinyInteger('comfort_rating')->nullable();
            $table->unsignedTinyInteger('staff_rating')->nullable();
            $table->unsignedTinyInteger('food_rating')->nullable();
            $table->unsignedTinyInteger('entertainment_rating')->nullable();
            $table->unsignedTinyInteger('punctuality_rating')->nullable();
            $table->unsignedTinyInteger('value_rating')->nullable();
            $table->unsignedTinyInteger('cleanliness_rating')->nullable();
            $table->unsignedTinyInteger('checkin_rating')->nullable();

            // Review Content
            $table->string('title')->nullable();
            $table->text('review');
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();

            // Trip Details
            $table->string('travel_class')->nullable()->comment('economy, business, first');
            $table->enum('traveler_type', [
                'solo',
                'couple',
                'family',
                'business',
                'group',
            ])->nullable();
            $table->date('travel_date')->nullable();
            $table->string('route')->nullable()->comment('DAC-DXB');

            // Helpful Votes
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);

            // Status
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Moderation
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->text('admin_notes')->nullable();

            // Reviewer Info (for non-logged-in reviews)
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_email')->nullable();
            $table->string('reviewer_country')->nullable();

            // Response
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_responded_at')->nullable();
            $table->foreignId('admin_responded_by')->nullable()->constrained('users')->nullOnDelete();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['airline_id', 'is_approved']);
            $table->index(['booking_id', 'is_approved']);
            $table->index('overall_rating');
            $table->index(['is_approved', 'is_featured']);
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_reviews');
    }
};
