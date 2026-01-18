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
        Schema::create('flight_fares', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Flight & Class
            $table->foreignId('flight_id')->constrained('flights')->cascadeOnDelete();
            $table->foreignId('fare_class_id')->constrained('flight_fare_classes')->cascadeOnDelete();

            // Date Range
            $table->date('valid_from');
            $table->date('valid_until');

            // Pricing per Passenger Type
            $table->decimal('adult_base_fare', 12, 2);
            $table->decimal('adult_tax', 12, 2)->default(0);
            $table->decimal('adult_fee', 12, 2)->default(0)->comment('Service/fuel surcharge');
            $table->decimal('adult_total', 12, 2);

            $table->decimal('child_base_fare', 12, 2)->nullable();
            $table->decimal('child_tax', 12, 2)->default(0);
            $table->decimal('child_fee', 12, 2)->default(0);
            $table->decimal('child_total', 12, 2)->nullable();
            $table->unsignedTinyInteger('child_discount_percent')->default(25);

            $table->decimal('infant_base_fare', 12, 2)->nullable();
            $table->decimal('infant_tax', 12, 2)->default(0);
            $table->decimal('infant_fee', 12, 2)->default(0);
            $table->decimal('infant_total', 12, 2)->nullable();
            $table->unsignedTinyInteger('infant_discount_percent')->default(90);

            // Currency
            $table->string('currency_code', 3)->default('BDT');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);

            // Tax Breakdown (for countries requiring detailed tax display)
            $table->json('tax_breakdown')->nullable()->comment('YQ, YR, BD tax etc.');

            // Inventory
            $table->unsignedSmallInteger('total_seats')->default(0);
            $table->unsignedSmallInteger('available_seats')->default(0);
            $table->unsignedSmallInteger('sold_seats')->default(0);
            $table->unsignedSmallInteger('blocked_seats')->default(0)->comment('Held/reserved');

            // Cost & Profit (for commission tracking)
            $table->decimal('net_cost', 12, 2)->nullable()->comment('Supplier cost');
            $table->decimal('markup_amount', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);

            // Supplier Reference
            $table->string('supplier_fare_id')->nullable();
            $table->json('supplier_fare_data')->nullable()->comment('Raw supplier response');

            // Restrictions
            $table->unsignedSmallInteger('min_booking_days')->default(0)->comment('Advance purchase');
            $table->unsignedSmallInteger('max_booking_days')->nullable();
            $table->boolean('is_combinable')->default(true)->comment('Can combine with other fares');

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_soldout')->default(false);
            $table->timestamp('last_synced_at')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['flight_id', 'fare_class_id']);
            $table->index(['valid_from', 'valid_until']);
            $table->index(['is_active', 'is_soldout']);
            $table->index('available_seats');
            $table->index('adult_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_fares');
    }
};
