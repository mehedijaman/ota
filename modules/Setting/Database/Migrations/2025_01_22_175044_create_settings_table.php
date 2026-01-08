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

            // Site Identity
            $table->string('site_name')->nullable();
            $table->text('description')->nullable();
            $table->string('slogan')->nullable();

            // Display Options
            $table->boolean('show_name')->default(true);
            $table->boolean('show_slogan')->default(true);
            $table->boolean('show_logo')->default(true);

            // Contact Information
            $table->json('addresses')->nullable();
            $table->json('phones')->nullable();
            $table->json('emails')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('google_map')->nullable();

            // Social Media Links
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('pinterest')->nullable();

            // Theme Customization
            $table->string('navbar_background')->nullable();
            $table->string('navbar_text_color')->nullable();
            $table->string('body_background')->nullable();
            $table->string('body_text_color')->nullable();
            $table->string('footer_background')->nullable();
            $table->string('footer_text_color')->nullable();

            $table->json('smtp_settings')->nullable();

            // Advanced
            $table->longText('custom_css')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
