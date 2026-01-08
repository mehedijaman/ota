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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->comment('AI Provider: OpenAI, Gemini, etc.');
            $table->string('model')->comment('Model name: gpt-4, gemini-pro, etc.');
            $table->string('api_key')->comment('API Key for the provider');
            $table->boolean('is_default')->default(false)->comment('Default AI Model');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
