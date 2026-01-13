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
        Schema::create('visa_processing_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_id')->constrained('visas')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['normal', 'express', 'urgent', 'super_urgent'])->default('normal');
            $table->unsignedSmallInteger('processing_days_min');
            $table->unsignedSmallInteger('processing_days_max');
            $table->decimal('additional_fee', 12, 2)->default(0);
            $table->boolean('is_guaranteed')->default(false)->comment('Guaranteed processing time');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['visa_id', 'is_active']);
            $table->index(['visa_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_processing_times');
    }
};
