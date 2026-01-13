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
        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('visa_id')->constrained('visas')->restrictOnDelete();
            $table->foreignId('country_id')->constrained('visa_countries')->restrictOnDelete();
            $table->foreignId('processing_time_id')->nullable()->constrained('visa_processing_times')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();

            // Application Status
            $table->enum('status', [
                'draft',
                'pending',
                'documents_pending',
                'documents_submitted',
                'under_review',
                'processing',
                'submitted_to_embassy',
                'interview_scheduled',
                'interview_completed',
                'approved',
                'rejected',
                'on_hold',
                'cancelled',
                'completed',
            ])->default('draft');

            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');

            // Traveler counts
            $table->unsignedSmallInteger('adult_count')->default(1);
            $table->unsignedSmallInteger('child_count')->default(0);
            $table->unsignedSmallInteger('infant_count')->default(0);

            // Travel Information
            $table->date('intended_travel_date')->nullable();
            $table->date('intended_return_date')->nullable();
            $table->string('travel_purpose')->nullable();
            $table->text('travel_itinerary')->nullable();
            $table->string('port_of_entry')->nullable();
            $table->text('accommodation_details')->nullable();
            $table->string('sponsor_name')->nullable();
            $table->text('sponsor_address')->nullable();
            $table->string('sponsor_phone')->nullable();
            $table->string('sponsor_relationship')->nullable();

            // Contact Information
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Financial Information
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('embassy_fees', 12, 2)->default(0);
            $table->decimal('service_fees', 12, 2)->default(0);
            $table->decimal('express_fees', 12, 2)->default(0);
            $table->decimal('additional_fees', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2)->default(0);

            // Embassy/Interview Details
            $table->timestamp('interview_date')->nullable();
            $table->string('interview_time')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            $table->string('biometric_date')->nullable();
            $table->string('biometric_location')->nullable();
            $table->string('appointment_reference')->nullable();

            // Submission Tracking
            $table->date('documents_submitted_date')->nullable();
            $table->date('embassy_submission_date')->nullable();
            $table->string('embassy_reference_number')->nullable();
            $table->date('expected_result_date')->nullable();
            $table->date('passport_collection_date')->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_issue_date')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->string('visa_sticker_number')->nullable();

            // Source & Tracking
            $table->enum('application_source', ['website', 'admin', 'agent', 'api', 'walk_in', 'phone'])->default('website');
            $table->string('referral_source')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Notes
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // User tracking
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['visa_id', 'status']);
            $table->index(['country_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['agent_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('intended_travel_date');
            $table->index('created_at');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_applications');
    }
};
