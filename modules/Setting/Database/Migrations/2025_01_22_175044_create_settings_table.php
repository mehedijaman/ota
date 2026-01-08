<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Logical grouping
            $table->string('group', 100)->index();      // site, contact, social, theme, mail, security
            $table->string('key', 150)->index();        // site_name, show_logo, smtp, etc.

            // Value storage
            $table->longText('value')->nullable();      // JSON / string / boolean / array

            // Metadata
            $table->string('type', 50)->default('string')->index();
            // string | boolean | json | integer | color | css | html

            $table->string('context', 50)->default('global')->index();
            // global | web | mobile | admin | api

            // Control
            $table->boolean('is_encrypted')->default(false)->index();
            $table->boolean('is_public')->default(false)->index(); // frontend-safe
            $table->boolean('is_active')->default(true)->index();

            // Optional description (Admin UI helper)
            $table->string('label')->nullable();
            $table->text('description')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['group', 'key', 'context']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
