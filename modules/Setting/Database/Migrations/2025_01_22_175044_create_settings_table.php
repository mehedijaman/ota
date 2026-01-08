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

            /* Homepage Settings */
            $table->boolean('show_topbar')->nullable()->default(1);
            $table->boolean('show_navbar')->nullable()->default(1);
            $table->boolean('show_slider')->nullable()->default(1);
            $table->boolean('show_latest_products')->nullable()->default(1);
            $table->boolean('show_featured_products')->nullable()->default(1);
            $table->boolean('show_brands')->nullable()->default(1);
            $table->boolean('show_testimonials')->nullable()->default(1);
            $table->boolean('show_latest_posts')->nullable()->default(1);
            $table->boolean('show_footer_menu')->nullable()->default(1);
            $table->boolean('show_floating_chat')->nullable()->default(1);
            $table->boolean('show_newsletter')->nullable()->default(1);
            $table->boolean('show_copyright')->nullable()->default(1);
            $table->boolean('show_back_to_top')->nullable()->default(1);

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
            $table->boolean('show_favicon')->nullable()->default(1);
            $table->boolean('show_language_switcher')->nullable()->default(0);
            $table->string('currency_symbol')->nullable();
            $table->string('default_language')->nullable();
            $table->string('default_timezone')->nullable();
            $table->string('default_currency')->nullable();
            $table->boolean('show_currency_switcher')->nullable()->default(0);
            $table->boolean('show_theme_switcher')->nullable()->default(0);
            $table->boolean('show_layout_switcher')->nullable()->default(0);
            $table->boolean('show_direction_switcher')->nullable()->default(0);
            $table->boolean('show_rtl')->nullable()->default(0);
            $table->boolean('show_ltr')->nullable()->default(0);

            /* Color settings */
            $table->string('topbar_background')->nullable();
            $table->string('navbar_background')->nullable();
            $table->string('body_background')->nullable();
            $table->string('footer_background')->nullable();
            $table->string('topbar_text_color')->nullable();
            $table->string('navbar_text_color')->nullable();
            $table->string('body_text_color')->nullable();
            $table->string('footer_text_color')->nullable();

            /* Product settings */
            $table->boolean('show_view_details_button')->nullable()->default(1);
            $table->boolean('show_add_to_cart_button')->nullable()->default(1);
            $table->boolean('show_add_to_wishlist_button')->nullable()->default(1);
            $table->boolean('show_add_to_compare_button')->nullable()->default(1);
            $table->boolean('show_product_rating')->nullable()->default(1);
            $table->boolean('show_product_review')->nullable()->default(1);
            $table->boolean('show_product_discount')->nullable()->default(1);
            $table->boolean('show_product_shipping')->nullable()->default(1);
            $table->boolean('show_product_stock')->nullable()->default(1);
            $table->boolean('show_cart_menu')->nullable()->default(1);

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
