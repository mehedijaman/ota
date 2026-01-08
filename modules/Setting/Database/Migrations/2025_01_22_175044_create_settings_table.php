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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            /* Contact Settings */
            $table->json('addresses')->nullable();
            $table->text('google_map')->nullable();
            $table->json('phones')->nullable();
            $table->string('whatsapp')->nullable();
            $table->json('emails')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('pinterest')->nullable();

            /* Site General Settings */
            $table->string('site_name')->nullable();
            $table->string('slogan')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('banner')->nullable();
            $table->boolean('show_name')->nullable()->default(1);
            $table->boolean('show_slogan')->nullable()->default(1);
            $table->boolean('show_logo')->nullable()->default(1);

            /* Color settings */
            $table->string('navbar_background')->nullable();
            $table->string('body_background')->nullable();
            $table->string('footer_background')->nullable();
            $table->string('navbar_text_color')->nullable();
            $table->string('body_text_color')->nullable();
            $table->string('footer_text_color')->nullable();

            /* Additional Settings */
            $table->longText('custom_css')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
