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
        Schema::create('hotel_suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code')->unique()->comment('Internal code: agoda, booking, expedia, manual, etc.');
            $table->enum('type', ['api', 'xml_feed', 'manual', 'gds'])->default('api');
            $table->string('api_endpoint')->nullable();
            $table->string('api_version')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('priority')->default(100)->comment('Lower = higher priority in search');
            $table->decimal('commission_rate', 5, 2)->default(0)->comment('Default commission percentage');
            $table->decimal('markup_rate', 5, 2)->default(0)->comment('Default markup percentage');
            $table->unsignedInteger('timeout_seconds')->default(30)->comment('API timeout');
            $table->unsignedInteger('retry_attempts')->default(3)->comment('Retry on failure');
            $table->json('settings')->nullable()->comment('Supplier-specific configuration');
            $table->json('supported_currencies')->nullable();
            $table->json('supported_languages')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'priority']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_suppliers');
    }
};
