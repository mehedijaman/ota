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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('supplier_id')->nullable()->constrained('hotel_suppliers')->nullOnDelete()->comment('NULL for manual inventory');
            $table->string('supplier_hotel_id')->nullable()->comment('External ID from supplier');
            $table->foreignId('chain_id')->nullable()->constrained('hotel_chains')->nullOnDelete()->comment('NULL for independent');
            $table->foreignId('country_id')->constrained('hotel_countries')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('hotel_cities')->restrictOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('hotel_areas')->nullOnDelete();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique()->nullable()->comment('Internal hotel code');
            $table->unsignedTinyInteger('star_rating')->nullable()->comment('1-5 stars');
            $table->enum('property_type', [
                'hotel',
                'resort',
                'apartment',
                'villa',
                'hostel',
                'guesthouse',
                'boutique',
                'motel',
                'lodge',
                'inn',
                'bed_and_breakfast',
                'homestay',
            ])->default('hotel');

            // Descriptions
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            // Location
            $table->string('address');
            $table->string('address_line_2')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('fax')->nullable();

            // Operational
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->time('check_in_time_end')->nullable()->comment('Latest check-in');
            $table->unsignedSmallInteger('total_rooms')->default(0);
            $table->unsignedSmallInteger('total_floors')->nullable();
            $table->year('year_built')->nullable();
            $table->year('year_renovated')->nullable();

            // Policies
            $table->json('policies')->nullable()->comment('House policies JSON');
            $table->json('child_policies')->nullable();
            $table->json('pet_policies')->nullable();
            $table->boolean('accepts_credit_cards')->default(true);
            $table->json('accepted_cards')->nullable();

            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_instant_bookable')->default(false);

            // Ratings & Stats
            $table->decimal('avg_rating', 3, 2)->default(0)->comment('Calculated 0-5');
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('bookings_count')->default(0);

            // Commission & Markup
            $table->decimal('commission_rate', 5, 2)->nullable()->comment('Override supplier default');
            $table->decimal('markup_rate', 5, 2)->nullable()->comment('Override supplier default');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['supplier_id', 'supplier_hotel_id']);
            $table->index(['country_id', 'is_active']);
            $table->index(['city_id', 'is_active']);
            $table->index(['area_id', 'is_active']);
            $table->index(['chain_id', 'is_active']);
            $table->index(['star_rating', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['is_recommended', 'is_active']);
            $table->index(['latitude', 'longitude']);
            $table->index('property_type');
            $table->index('avg_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
