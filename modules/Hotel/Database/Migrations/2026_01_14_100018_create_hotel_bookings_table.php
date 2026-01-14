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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('booking_number')->unique();
            $table->foreignId('hotel_id')->constrained('hotels')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('hotel_suppliers')->nullOnDelete();
            $table->string('supplier_booking_id')->nullable()->comment('External confirmation number');
            $table->string('supplier_confirmation_code')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();

            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'guaranteed',
                'modification_pending',
                'checked_in',
                'checked_out',
                'completed',
                'cancelled',
                'no_show',
                'refunded',
            ])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');

            // Dates
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedSmallInteger('nights')->default(1);

            // Room & Guest Counts
            $table->unsignedTinyInteger('total_rooms')->default(1);
            $table->unsignedSmallInteger('total_adults')->default(1);
            $table->unsignedSmallInteger('total_children')->default(0);
            $table->unsignedSmallInteger('total_infants')->default(0);

            // Primary Contact
            $table->string('contact_title')->nullable();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_postal_code')->nullable();
            $table->string('contact_country')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Special Requests
            $table->text('special_requests')->nullable();
            $table->time('estimated_arrival_time')->nullable();
            $table->boolean('late_checkout_requested')->default(false);
            $table->boolean('early_checkin_requested')->default(false);

            // Financial
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Room rates total');
            $table->decimal('extras_total', 12, 2)->default(0)->comment('Extra charges');
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('cost_amount', 12, 2)->default(0)->comment('Net cost');
            $table->decimal('profit_amount', 12, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2)->default(0);
            $table->date('payment_due_date')->nullable();

            // Source & Tracking
            $table->enum('booking_source', [
                'website',
                'admin',
                'agent',
                'api',
                'phone',
                'walk_in',
                'email',
                'whatsapp',
            ])->default('website');
            $table->string('referral_source')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Notes
            $table->text('internal_notes')->nullable();
            $table->text('guest_notes')->nullable();
            $table->text('supplier_notes')->nullable();

            // Status Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('no_show_at')->nullable();
            $table->timestamp('modification_requested_at')->nullable();

            // User Tracking
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['hotel_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['agent_id', 'status']);
            $table->index(['supplier_id', 'supplier_booking_id']);
            $table->index('check_in_date');
            $table->index('check_out_date');
            $table->index(['check_in_date', 'check_out_date']);
            $table->index(['status', 'payment_status']);
            $table->index('created_at');
            $table->index('assigned_to');
            $table->index('booking_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
