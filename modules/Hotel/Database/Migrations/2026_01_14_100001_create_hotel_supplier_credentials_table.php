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
        Schema::create('hotel_supplier_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('hotel_suppliers')->cascadeOnDelete();
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->text('api_key')->nullable()->comment('Encrypted API key');
            $table->text('api_secret')->nullable()->comment('Encrypted API secret');
            $table->string('username')->nullable();
            $table->text('password')->nullable()->comment('Encrypted password');
            $table->text('access_token')->nullable()->comment('Current OAuth token');
            $table->timestamp('token_expires_at')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('certificate')->nullable()->comment('SSL certificate if required');
            $table->text('private_key')->nullable()->comment('Private key if required');
            $table->json('additional_config')->nullable()->comment('Extra credentials');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_failure_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'environment']);
            $table->index(['supplier_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_supplier_credentials');
    }
};
