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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();

            $table->foreignId('faq_category_id')->nullable()->constrained('faq_categories')->nullOnDelete();

            $table->string('question', 500)->index();
            $table->longText('answer');

            // Visibility & Lifecycle
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();

            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();

            // Targeting (Optional)
            $table->enum('target_audience', ['all', 'b2c', 'b2b'])->default('all')->index();

            // SEO (Optional)
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
