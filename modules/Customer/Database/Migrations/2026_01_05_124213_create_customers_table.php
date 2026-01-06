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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();

            // Identity
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('gender', 20)->nullable()->index();
            $table->date('date_of_birth')->nullable()->index();

            $table->string('email')->unique();
            $table->string('phone', 50)->nullable()->index();

            // Address
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable()->index();
            $table->string('state', 100)->nullable()->index();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable()->index();

            // Identification
            $table->string('nid_no', 100)->nullable()->index();
            $table->string('passport_no', 100)->nullable()->index();
            $table->date('passport_expiry')->nullable()->index();

            // Account
            $table->enum('status', ['active', 'inactive', 'suspended', 'blocked'])->default('active')->index();

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
        Schema::dropIfExists('customers');
    }
};
