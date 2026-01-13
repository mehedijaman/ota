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
        Schema::create('visa_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 3)->unique()->comment('ISO 3166-1 alpha-2 or alpha-3 code');
            $table->string('flag_icon')->nullable()->comment('Flag emoji or icon class');
            $table->string('continent')->nullable();
            $table->string('region')->nullable();
            $table->string('capital')->nullable();
            $table->string('timezone')->nullable();
            $table->text('description')->nullable();
            $table->longText('visa_info')->nullable()->comment('General visa information for this country');
            $table->string('embassy_name')->nullable();
            $table->text('embassy_address')->nullable();
            $table->string('embassy_phone')->nullable();
            $table->string('embassy_email')->nullable();
            $table->string('embassy_website')->nullable();
            $table->json('embassy_working_hours')->nullable();
            $table->string('consulate_name')->nullable();
            $table->text('consulate_address')->nullable();
            $table->string('consulate_phone')->nullable();
            $table->string('consulate_email')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_popular']);
            $table->index(['is_active', 'sort_order']);
            $table->index('continent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_countries');
    }
};
