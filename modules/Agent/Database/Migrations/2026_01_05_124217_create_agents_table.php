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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();

            // Company & Contact
            $table->string('company_name', 255)->index();
            $table->date('established_at')->nullable();
            $table->string('contact_person', 255)->index();
            $table->string('email', 255)->unique()->index();
            $table->string('phone', 50)->nullable()->index();

            // Company & Contact
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Company & Contact
            $table->string('trade_license_no', 100)->nullable();
            $table->string('mocat_no', 100)->nullable();
            $table->string('nid_no', 100)->nullable();
            $table->string('tin_no', 100)->nullable();

            $table->enum('status', ['pending', 'approved', 'suspended', 'inactive'])->default('pending')->index();
            $table->text('remarks')->nullable();

            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->default(0);

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
        Schema::dropIfExists('agents');
    }
};
