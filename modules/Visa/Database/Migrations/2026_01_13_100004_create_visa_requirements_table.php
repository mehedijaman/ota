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
        Schema::create('visa_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_id')->constrained('visas')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->enum('type', ['document', 'form', 'photo', 'biometric', 'financial', 'travel', 'personal', 'information', 'other'])->default('document');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable()->comment('How to prepare/obtain this requirement');
            $table->text('sample_format')->nullable()->comment('Sample or template description');
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('needs_upload')->default(true)->comment('Whether applicant needs to upload this');
            $table->boolean('needs_original')->default(false)->comment('Original document required');
            $table->boolean('needs_notarization')->default(false);
            $table->boolean('needs_translation')->default(false);
            $table->boolean('needs_attestation')->default(false);
            $table->unsignedSmallInteger('copies_required')->default(1);
            $table->json('accepted_formats')->nullable()->comment('Accepted file formats like pdf, jpg');
            $table->unsignedInteger('max_file_size_kb')->nullable();
            $table->enum('applicant_type', ['all', 'adult', 'child', 'employed', 'student', 'businessman', 'retired'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['visa_id', 'slug']);
            $table->index(['visa_id', 'is_mandatory', 'is_active']);
            $table->index(['visa_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_requirements');
    }
};
