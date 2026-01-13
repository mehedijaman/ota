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
        Schema::create('visa_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_id')->constrained('visas')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('fee_type', ['embassy', 'service', 'vfs', 'courier', 'insurance', 'express', 'other'])->default('service');
            $table->enum('applicant_type', ['all', 'adult', 'child', 'infant', 'senior'])->default('all');
            $table->string('nationality_code', 3)->nullable()->comment('Specific nationality pricing, null for all');
            $table->decimal('amount', 12, 2)->default(0);
            $table->boolean('is_per_person')->default(true);
            $table->boolean('is_refundable')->default(false);
            $table->boolean('is_included_in_package')->default(false);
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('sort_order')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['visa_id', 'is_active']);
            $table->index(['visa_id', 'fee_type']);
            $table->index(['visa_id', 'applicant_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_fees');
    }
};
