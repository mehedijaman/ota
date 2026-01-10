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
        Schema::create('tour_booking_travelers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('tour_bookings')->cascadeOnDelete();
            $table->enum('type', ['adult', 'child', 'infant'])->default('adult');
            $table->boolean('is_lead')->default(false);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('passport_country')->nullable();
            $table->string('nid_number')->nullable();
            $table->text('dietary_requirements')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->string('room_preference')->nullable();
            $table->text('special_requests')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'is_lead']);
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_booking_travelers');
    }
};
