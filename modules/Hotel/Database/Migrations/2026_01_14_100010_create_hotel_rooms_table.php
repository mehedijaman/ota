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
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('hotel_room_types')->nullOnDelete();
            $table->string('supplier_room_id')->nullable()->comment('External ID from supplier');

            // Basic Information
            $table->string('name');
            $table->string('code')->nullable()->comment('Internal room code');
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            // Bed Configuration
            $table->enum('bed_type', [
                'single',
                'double',
                'queen',
                'king',
                'twin',
                'triple',
                'bunk',
                'sofa_bed',
                'murphy',
                'water_bed',
                'mixed',
            ])->nullable();
            $table->unsignedTinyInteger('bed_count')->default(1);
            $table->json('bed_configuration')->nullable()->comment('Detailed bed setup');

            // Occupancy
            $table->unsignedTinyInteger('max_adults')->default(2);
            $table->unsignedTinyInteger('max_children')->default(0);
            $table->unsignedTinyInteger('max_infants')->default(0);
            $table->unsignedTinyInteger('max_occupancy')->default(2);
            $table->unsignedTinyInteger('standard_occupancy')->default(2);

            // Room Details
            $table->decimal('room_size_sqm', 6, 2)->nullable();
            $table->decimal('room_size_sqft', 6, 2)->nullable();
            $table->string('floor')->nullable()->comment('Floor number/name');
            $table->string('view_type')->nullable()->comment('Ocean, City, Garden, etc.');
            $table->boolean('is_smoking')->default(false);
            $table->boolean('is_accessible')->default(false)->comment('Wheelchair accessible');
            $table->boolean('has_balcony')->default(false);
            $table->boolean('has_terrace')->default(false);
            $table->boolean('has_kitchen')->default(false);
            $table->boolean('has_kitchenette')->default(false);

            // Inventory
            $table->unsignedSmallInteger('total_inventory')->default(1)->comment('Total physical rooms');

            // Media
            $table->json('images')->nullable()->comment('Room images array');

            // Status
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['hotel_id', 'is_active']);
            $table->index(['room_type_id', 'is_active']);
            $table->index('supplier_room_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
