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
        Schema::create('flight_suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 20)->unique()->comment('Internal supplier code');
            $table->enum('type', [
                'gds',           // Global Distribution System (Amadeus, Sabre, Travelport)
                'lcc_api',       // Low Cost Carrier Direct API
                'consolidator',  // Consolidator/Wholesaler
                'direct',        // Direct airline contract
                'ndc',           // New Distribution Capability
                'aggregator',    // Aggregator (Skyscanner, Kayak)
            ])->default('gds');

            // API Configuration
            $table->string('api_base_url')->nullable();
            $table->string('api_version')->nullable();
            $table->json('api_credentials')->nullable()->comment('Encrypted API keys/secrets');
            $table->json('api_settings')->nullable()->comment('Additional API configuration');
            $table->enum('auth_type', ['basic', 'oauth2', 'api_key', 'certificate', 'soap_header'])->default('api_key');

            // Operational Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_production')->default(false)->comment('Production vs Sandbox');
            $table->unsignedSmallInteger('priority')->default(100)->comment('Search priority order');
            $table->unsignedSmallInteger('timeout_seconds')->default(30);
            $table->unsignedSmallInteger('max_retries')->default(3);

            // Financial
            $table->decimal('default_commission_rate', 5, 2)->default(0);
            $table->decimal('default_markup_rate', 5, 2)->default(0);
            $table->enum('commission_type', ['percentage', 'fixed', 'per_segment', 'per_passenger'])->default('percentage');
            $table->string('currency_code', 3)->default('BDT');

            // Capabilities
            $table->json('supported_features')->nullable()->comment('booking, cancel, void, refund, exchange, ancillaries');
            $table->json('supported_regions')->nullable()->comment('Regions/countries supported');
            $table->json('excluded_airlines')->nullable()->comment('Airlines not to book through this supplier');

            // Contact
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['is_active', 'priority']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_suppliers');
    }
};
