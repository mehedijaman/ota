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
        Schema::create('flight_search_logs', function (Blueprint $table) {
            $table->id();

            // User Context
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->string('session_id')->nullable();

            // Search Parameters
            $table->enum('trip_type', ['one_way', 'round_trip', 'multi_city'])->default('one_way');
            $table->foreignId('origin_airport_id')->nullable()->constrained('flight_airports')->nullOnDelete();
            $table->foreignId('destination_airport_id')->nullable()->constrained('flight_airports')->nullOnDelete();
            $table->string('origin_code', 3)->nullable();
            $table->string('destination_code', 3)->nullable();
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('infants')->default(0);
            $table->enum('cabin_class', ['economy', 'premium_economy', 'business', 'first', 'any'])->default('economy');
            $table->boolean('direct_only')->default(false);
            $table->boolean('flexible_dates')->default(false);
            $table->string('preferred_airlines')->nullable();

            // Results
            $table->unsignedSmallInteger('results_count')->default(0);
            $table->decimal('lowest_price', 12, 2)->nullable();
            $table->decimal('highest_price', 12, 2)->nullable();
            $table->string('currency_code', 3)->default('BDT');

            // Performance
            $table->unsignedSmallInteger('response_time_ms')->nullable();
            $table->json('suppliers_queried')->nullable();
            $table->json('supplier_response_times')->nullable();

            // Source
            $table->enum('source', ['website', 'mobile_app', 'api', 'agent_portal'])->default('website');
            $table->string('device_type')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Conversion
            $table->boolean('converted_to_booking')->default(false);
            $table->foreignId('booking_id')->nullable()->constrained('flight_bookings')->nullOnDelete();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['origin_airport_id', 'destination_airport_id']);
            $table->index(['origin_code', 'destination_code']);
            $table->index('departure_date');
            $table->index(['departure_date', 'cabin_class']);
            $table->index('customer_id');
            $table->index('session_id');
            $table->index('created_at');
            $table->index('converted_to_booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_search_logs');
    }
};
