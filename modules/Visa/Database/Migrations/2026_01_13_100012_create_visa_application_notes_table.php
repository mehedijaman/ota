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
        Schema::create('visa_application_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('visa_applications')->cascadeOnDelete();
            $table->enum('type', ['internal', 'customer', 'system', 'embassy', 'agent'])->default('internal');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_important')->default(false);
            $table->boolean('is_visible_to_customer')->default(false);
            $table->boolean('is_visible_to_agent')->default(false);
            $table->boolean('notify_customer')->default(false);
            $table->boolean('notify_agent')->default(false);
            $table->timestamp('notified_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['application_id', 'type']);
            $table->index(['application_id', 'is_pinned']);
            $table->index(['application_id', 'is_important']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_application_notes');
    }
};
