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
        Schema::create('visa_application_travelers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('visa_applications')->cascadeOnDelete();
            $table->enum('type', ['adult', 'child', 'infant'])->default('adult');
            $table->boolean('is_lead')->default(false)->comment('Primary applicant/contact');

            // Personal Information
            $table->string('title', 20)->nullable()->comment('Mr, Mrs, Ms, Dr, etc.');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('country_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('secondary_nationality')->nullable();
            $table->string('religion')->nullable();

            // Passport Information
            $table->string('passport_number')->nullable();
            $table->string('passport_type', 20)->nullable()->comment('Ordinary, Diplomatic, Official, etc.');
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_issue_place')->nullable();
            $table->string('passport_issue_authority')->nullable();
            $table->string('old_passport_number')->nullable();
            $table->date('old_passport_expiry')->nullable();

            // National ID
            $table->string('nid_number')->nullable();
            $table->string('birth_certificate_number')->nullable();
            $table->string('tin_number')->nullable()->comment('Tax Identification Number');

            // Address Information
            $table->text('present_address')->nullable();
            $table->string('present_city')->nullable();
            $table->string('present_postal_code')->nullable();
            $table->string('present_country')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_postal_code')->nullable();
            $table->string('permanent_country')->nullable();

            // Employment Information
            $table->enum('employment_status', ['employed', 'self_employed', 'student', 'retired', 'unemployed', 'homemaker', 'minor'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->text('employer_address')->nullable();
            $table->string('employer_phone')->nullable();
            $table->string('job_title')->nullable();
            $table->date('employment_start_date')->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();

            // Education Information (for students)
            $table->string('institution_name')->nullable();
            $table->text('institution_address')->nullable();
            $table->string('course_name')->nullable();
            $table->date('course_start_date')->nullable();
            $table->date('course_end_date')->nullable();

            // Financial Information
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->decimal('bank_balance', 15, 2)->nullable();
            $table->string('financial_sponsor')->nullable();
            $table->string('financial_sponsor_relationship')->nullable();

            // Family Information
            $table->string('father_name')->nullable();
            $table->string('father_nationality')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_nationality')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_nationality')->nullable();
            $table->date('spouse_date_of_birth')->nullable();

            // Travel History
            $table->boolean('has_previous_visa')->default(false);
            $table->text('previous_visa_details')->nullable();
            $table->boolean('has_visa_refusal')->default(false);
            $table->text('visa_refusal_details')->nullable();
            $table->boolean('has_deportation_history')->default(false);
            $table->text('deportation_details')->nullable();
            $table->json('countries_visited')->nullable()->comment('Array of recently visited countries');

            // Health & Background
            $table->text('medical_conditions')->nullable();
            $table->boolean('has_criminal_record')->default(false);
            $table->text('criminal_record_details')->nullable();

            // Additional Information
            $table->text('special_requests')->nullable();
            $table->json('additional_data')->nullable()->comment('Any extra form fields');

            // Document verification status
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['application_id', 'is_lead']);
            $table->index(['application_id', 'type']);
            $table->index('passport_number');
            $table->index('nid_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_application_travelers');
    }
};
