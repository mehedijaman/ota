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
        Schema::create('visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('visa_countries')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('visa_categories')->nullOnDelete();
            $table->foreignId('visa_type_id')->nullable()->constrained('visa_types')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique()->nullable()->comment('Internal visa package code');
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->longText('eligibility_criteria')->nullable();
            $table->longText('terms_conditions')->nullable();
            $table->longText('important_notes')->nullable();
            $table->enum('entry_type', ['single', 'double', 'multiple'])->default('single');
            $table->unsignedSmallInteger('validity_days')->nullable();
            $table->unsignedSmallInteger('stay_days')->nullable();
            $table->unsignedSmallInteger('processing_days_min')->nullable();
            $table->unsignedSmallInteger('processing_days_max')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('embassy_fee', 12, 2)->default(0);
            $table->decimal('vfs_fee', 12, 2)->default(0)->comment('Visa Facilitation Service fee');
            $table->decimal('success_rate', 5, 2)->nullable()->comment('Visa approval success rate percentage');
            $table->boolean('is_express_available')->default(false);
            $table->boolean('is_interview_required')->default(false);
            $table->boolean('is_biometric_required')->default(false);
            $table->boolean('is_appointment_required')->default(false);
            $table->boolean('is_travel_insurance_required')->default(false);
            $table->boolean('is_bank_statement_required')->default(true);
            $table->boolean('is_invitation_letter_required')->default(false);
            $table->boolean('is_hotel_booking_required')->default(false);
            $table->boolean('is_flight_booking_required')->default(false);
            $table->json('included_services')->nullable();
            $table->json('excluded_services')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->unsignedInteger('applications_count')->default(0)->comment('Total applications counter');
            $table->unsignedInteger('approved_count')->default(0)->comment('Approved applications counter');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
            $table->index(['is_active', 'is_featured']);
            $table->index(['is_active', 'is_popular']);
            $table->index('valid_from');
            $table->index('valid_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visas');
    }
};
