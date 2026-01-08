<?php

namespace Modules\Testimonial\Actions;

use Modules\Testimonial\Models\Testimonial;

class GetFeaturedTestimonialsAction
{
    public static function handle(int $limit = 3)
    {
        $testimonials = Testimonial::where('featured', true)
            ->latest()
            ->limit($limit)
            ->get();

        return $testimonials;
    }
}
