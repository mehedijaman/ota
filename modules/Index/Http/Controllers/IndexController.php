<?php

namespace Modules\Index\Http\Controllers;

use Modules\Page\Models\Page;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Testimonial\Services\TestimonialService;

class IndexController extends SiteController
{
    public function index($slug = null)
    {
        if (is_null($slug)) {
            $topTestimonials = TestimonialService::getTopTestimonials(3);

            return view('index::index', [
                'topTestimonials' => $topTestimonials,
            ]);
        }

        $page = Page::where('slug', $slug)->first();

        if (is_null($page)) {
            abort(404);
        }

        return view('index::page', compact('page'));
    }

    public function about()
    {
        return view('index::about');
    }

    public function whyUs()
    {
        return view('index::why-us');
    }

    public function termsAndConditions()
    {
        return view('index::terms-and-conditions');
    }

    public function privacyPolicy()
    {
        return view('index::privacy-policy');
    }
}
