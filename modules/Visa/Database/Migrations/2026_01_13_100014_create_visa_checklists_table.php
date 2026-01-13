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
        Schema::create('visa_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_id')->constrained('visas')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('stage', ['pre_application', 'documentation', 'submission', 'post_approval'])->default('documentation');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['visa_id', 'stage', 'is_active']);
        });

        Schema::create('visa_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('visa_checklists')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['checklist_id', 'is_active', 'sort_order']);
        });

        Schema::create('visa_application_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('visa_applications')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('visa_checklist_items')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['application_id', 'checklist_item_id'], 'app_checklist_unique');
            $table->index(['application_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_application_checklist_items');
        Schema::dropIfExists('visa_checklist_items');
        Schema::dropIfExists('visa_checklists');
    }
};
