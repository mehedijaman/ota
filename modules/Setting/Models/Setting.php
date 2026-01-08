<?php

namespace Modules\Setting\Models;

use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Setting extends BaseModel
{
    use ActivityLog, Searchable;

    protected $table = 'settings';

    protected $appends = ['logo_url', 'favicon_url', 'watermark_url'];

    protected $casts = [
        'show_topbar' => 'boolean',
        'show_navbar' => 'boolean',
        'show_slider' => 'boolean',
        'show_top_categories' => 'boolean',
        'show_latest_products' => 'boolean',
        'show_featured_products' => 'boolean',
        'show_brands' => 'boolean',
        'show_testimonials' => 'boolean',
        'show_latest_posts' => 'boolean',
        'show_footer_menu' => 'boolean',
        'show_floating_chat' => 'boolean',

        'show_view_details_button' => 'boolean',
        'show_add_to_cart_button' => 'boolean',
        'show_add_to_wishlist_button' => 'boolean',
        'show_product_compare_button' => 'boolean',
        'show_product_rating' => 'boolean',
        'show_product_review' => 'boolean',
        'show_product_discount' => 'boolean',
        'show_product_shipping' => 'boolean',
        'show_product_stock' => 'boolean',

        'show_name' => 'boolean',
        'show_slogan' => 'boolean',
        'show_logo' => 'boolean',
        'show_favicon' => 'boolean',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset("storage/site/{$this->logo}");
        }

        return null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon) {
            return asset("storage/site/{$this->favicon}");
        }

        return null;
    }

    public function getWatermarkUrlAttribute(): ?string
    {
        $path = public_path('watermark.png');

        if (file_exists($path)) {
            return asset('watermark.png');
        }

        return null;
    }
}
