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
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('booking_number', 20)->unique()->comment('System booking reference');
            $table->string('pnr', 10)->nullable()->comment('Airline/GDS PNR');
            $table->string('supplier_booking_id')->nullable();
            $table->string('supplier_confirmation_code')->nullable();

            // Supplier
            $table->foreignId('supplier_id')->nullable()->constrained('flight_suppliers')->nullOnDelete();

            // Customer & Agent
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();

            // Trip Configuration
            $table->enum('trip_type', [
                'one_way',
                'round_trip',
                'multi_city',
                'open_jaw',
            ])->default('one_way');
            $table->enum('cabin_class', ['economy', 'premium_economy', 'business', 'first'])->default('economy');

            // Journey Dates
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->unsignedTinyInteger('total_segments')->default(1);

            // Passenger Counts
            $table->unsignedTinyInteger('adult_count')->default(1);
            $table->unsignedTinyInteger('child_count')->default(0);
            $table->unsignedTinyInteger('infant_count')->default(0);
            $table->unsignedTinyInteger('total_passengers')->default(1);

            // Status
            $table->enum('status', [
                'pending',           // Initial state
                'awaiting_payment',  // Payment required
                'processing',        // Being processed with supplier
                'ticketing',         // Tickets being issued
                'confirmed',         // Tickets issued
                'partially_flown',   // Some segments completed
                'completed',         // All segments flown
                'cancelled',         // Cancelled
                'void',              // Voided before ticketing
                'refund_pending',    // Refund requested
                'refunded',          // Refund processed
                'no_show',           // Passenger didn't show
                'schedule_changed',  // Airline changed schedule
            ])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded', 'failed'])->default('unpaid');
            $table->enum('ticketing_status', ['not_ticketed', 'ticketing', 'ticketed', 'void', 'reissued'])->default('not_ticketed');

            // Primary Contact
            $table->string('contact_title', 10)->nullable()->comment('Mr, Mrs, Ms');
            $table->string('contact_first_name');
            $table->string('contact_last_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('contact_phone_country_code', 5)->default('+880');
            $table->string('contact_whatsapp')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_postal_code')->nullable();
            $table->foreignId('contact_country_id')->nullable()->constrained('countries')->nullOnDelete();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Financial - Fare
            $table->decimal('base_fare_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('fee_total', 14, 2)->default(0)->comment('Fuel, service charges');

            // Financial - Extras
            $table->decimal('ancillary_total', 14, 2)->default(0)->comment('Baggage, seats, meals');
            $table->decimal('insurance_total', 14, 2)->default(0);

            // Financial - Adjustments
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('promo_discount', 14, 2)->default(0);
            $table->string('promo_code')->nullable();

            // Financial - Service Fees & Markup
            $table->decimal('service_fee', 14, 2)->default(0)->comment('OTA service fee');
            $table->decimal('convenience_fee', 14, 2)->default(0)->comment('Payment method fee');
            $table->decimal('markup_amount', 14, 2)->default(0);

            // Financial - Totals
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('amount_due', 14, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');

            // Cost Tracking
            $table->decimal('net_cost', 14, 2)->default(0)->comment('Supplier cost');
            $table->decimal('commission_earned', 14, 2)->default(0);
            $table->decimal('profit_amount', 14, 2)->default(0);

            // Payment Info
            $table->string('payment_method')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->timestamp('payment_deadline')->nullable()->comment('Time limit for payment');

            // Booking Source & Tracking
            $table->enum('booking_source', [
                'website',
                'mobile_app',
                'admin',
                'agent_portal',
                'api',
                'phone',
                'whatsapp',
                'email',
                'walk_in',
                'corporate',
            ])->default('website');
            $table->string('referral_source')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('affiliate_id')->nullable();
            $table->string('device_type')->nullable()->comment('desktop, mobile, tablet');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();

            // Special Requests
            $table->text('special_requests')->nullable();
            $table->text('meal_preferences')->nullable();
            $table->text('assistance_required')->nullable()->comment('Wheelchair, etc.');

            // Notes
            $table->text('internal_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('supplier_notes')->nullable();

            // Timestamps for Status Changes
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('ticketed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('void_at')->nullable();
            $table->timestamp('refund_requested_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('payment_received_at')->nullable();
            $table->timestamp('schedule_change_at')->nullable();

            // Time Limits
            $table->timestamp('hold_expires_at')->nullable()->comment('Ticketing time limit');
            $table->timestamp('fare_expires_at')->nullable();

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();

            // User Tracking
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ticketed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('pnr');
            $table->index(['supplier_id', 'supplier_booking_id']);
            $table->index(['customer_id', 'status']);
            $table->index(['agent_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('ticketing_status');
            $table->index('departure_date');
            $table->index(['departure_date', 'status']);
            $table->index('trip_type');
            $table->index('booking_source');
            $table->index('created_at');
            $table->index('assigned_to');
            $table->index('hold_expires_at');
            $table->index('payment_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
