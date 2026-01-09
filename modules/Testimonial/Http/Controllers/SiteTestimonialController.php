<?php

namespace Modules\Testimonial\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Testimonial\Services\TestimonialService;

class SiteTestimonialController extends SiteController
{
    public function index(): View
    {
        $testimonials = TestimonialService::getAllActive(9);

        return view('testimonial::index', [
            'testimonials' => $testimonials,
        ]);
    }
}
