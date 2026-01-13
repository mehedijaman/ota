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
        Schema::create('visa_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('visa_applications')->cascadeOnDelete();
            $table->foreignId('traveler_id')->nullable()->constrained('visa_application_travelers')->cascadeOnDelete();
            $table->foreignId('requirement_id')->nullable()->constrained('visa_requirements')->nullOnDelete();

            $table->string('document_type')->comment('passport, photo, bank_statement, etc.');
            $table->string('document_name');
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('disk')->default('local');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_hash')->nullable()->comment('MD5 hash for duplicate detection');

            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'needs_revision',
                'expired',
            ])->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->text('revision_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->date('document_issue_date')->nullable();
            $table->date('document_expiry_date')->nullable();

            $table->unsignedSmallInteger('version')->default(1)->comment('Document version for re-uploads');
            $table->boolean('is_latest')->default(true);

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['application_id', 'status']);
            $table->index(['application_id', 'document_type']);
            $table->index(['traveler_id', 'document_type']);
            $table->index('requirement_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_application_documents');
    }
};
