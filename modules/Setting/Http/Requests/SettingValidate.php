<?php

namespace Modules\Setting\Http\Requests;

use Modules\Support\Http\Requests\Request;

class SettingValidate extends Request
{
    public function rules(): array
    {
        return [
            /* Homepage Settings */
            'show_topbar' => 'nullable|boolean',
            'show_navbar' => 'nullable|boolean',
            'show_slider' => 'nullable|boolean',
            'show_top_categories' => 'nullable|boolean',
            'show_latest_products' => 'nullable|boolean',
            'show_featured_products' => 'nullable|boolean',
            'show_brands' => 'nullable|boolean',
            'show_testimonials' => 'nullable|boolean',
            'show_latest_posts' => 'nullable|boolean',
            'show_footer_menu' => 'nullable|boolean',
            'show_floating_chat' => 'nullable|boolean',

            /* Product Settings */
            'show_view_details_button' => 'nullable|boolean',
            'show_add_to_cart_button' => 'nullable|boolean',
            'show_add_to_wishlist_button' => 'nullable|boolean',
            'show_product_compare_button' => 'nullable|boolean',
            'show_product_rating' => 'nullable|boolean',
            'show_product_review' => 'nullable|boolean',
            'show_product_discount' => 'nullable|boolean',
            'show_product_shipping' => 'nullable|boolean',
            'show_product_stock' => 'nullable|boolean',

            /* Contact Settings */
            'addresses' => 'nullable|json',
            'google_map' => 'nullable|string',
            'phones' => 'nullable|json',
            'whatsapp' => 'nullable|string|max:255',
            'emails' => 'nullable|json',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',

            /* Site Settings */
            'site_name' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048', // Max size 2MB
            'favicon' => 'nullable|image|max:2048', // Max size 2MB
            'watermark' => 'nullable|image|max:2048', // Max size 2MB
            'show_name' => 'nullable|boolean',
            'show_slogan' => 'nullable|boolean',
            'show_logo' => 'nullable|boolean',
            'show_favicon' => 'nullable|boolean',
            'currency_symbol' => 'nullable|string|max:255',
        ];
    }
}
