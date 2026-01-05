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
        Schema::create('failed_imports', function (Blueprint $table) {
            $table->id();
            $table->string('import_type'); // e.g. 'students'
            $table->json('row_data');      // the original CSV row
            $table->string('reason')->nullable(); // validation or DB error message
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_imports');
    }
};
