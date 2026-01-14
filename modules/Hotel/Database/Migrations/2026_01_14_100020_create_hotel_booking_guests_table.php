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
        Schema::create('hotel_booking_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_room_id')->constrained('hotel_booking_rooms')->cascadeOnDelete();
            $table->enum('guest_type', ['adult', 'child', 'infant'])->default('adult');
            $table->boolean('is_primary')->default(false)->comment('Primary guest for this room');
            $table->boolean('is_lead_guest')->default(false)->comment('Lead guest for entire booking');

            // Personal Information
            $table->string('title')->nullable()->comment('Mr/Mrs/Ms/Dr');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedTinyInteger('age')->nullable();

            // Nationality & ID
            $table->string('nationality', 3)->nullable()->comment('ISO country code');
            $table->enum('id_type', [
                'passport',
                'nid',
                'driving_license',
                'national_id',
                'other',
            ])->nullable();
            $table->string('id_number')->nullable();
            $table->date('id_issue_date')->nullable();
            $table->date('id_expiry_date')->nullable();
            $table->string('id_issue_country', 3)->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Loyalty
            $table->string('loyalty_program')->nullable();
            $table->string('loyalty_number')->nullable();

            // Special Requirements
            $table->text('special_requirements')->nullable();
            $table->text('dietary_requirements')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->boolean('needs_wheelchair')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('booking_room_id');
            $table->index(['is_primary', 'booking_room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_guests');
    }
};
