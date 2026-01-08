<?php

namespace Modules\Testimonial\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Testimonial\Models\Testimonial;

class SiteTestimonialController extends SiteController
{
    public function index(): View
    {
        $testimonials = Testimonial::latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 9))
            ->withQueryString()
            ->through(fn ($testimonial) => [
                'id' => $testimonial->id,
                'name' => $testimonial->name,
                'designation' => $testimonial->designation,
                'company' => $testimonial->company,
                'title' => $testimonial->title,
                'description' => $testimonial->description,
                'image_url' => $testimonial->image_url,
                'active' => $testimonial->active,
            ]);

        return view('testimonial::testimonial-index', [
            'testimonials' => $testimonials,
        ]);
    }
}
