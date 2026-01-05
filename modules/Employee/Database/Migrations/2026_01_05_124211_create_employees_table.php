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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->foreignId('designation_id')->nullable()->constrained('employee_designations')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('employee_departments')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('employee_areas')->nullOnDelete();

            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->date('joining_date')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('emergency_contact')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->string('nid_no')->nullable()->unique();
            $table->string('passport_no')->nullable()->unique();
            $table->string('dl_no')->nullable()->unique();
            $table->string('tin_no')->nullable()->unique();

            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->nullable()->default(true);

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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('employees');
        Schema::enableForeignKeyConstraints();
    }
};
