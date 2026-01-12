<?php

namespace Modules\Index\Http\Controllers;

use Modules\Page\Models\Page;
use Modules\Blog\Services\PostService;
use Modules\Tour\Services\TourService;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Testimonial\Services\TestimonialService;

class IndexController extends SiteController
{
    public function index($slug = null)
    {
        if ($slug === null) {
            $topTestimonials = TestimonialService::getTopTestimonials(3);
            $latestPosts = PostService::getLatestPosts(3);
            $featuredTours = TourService::getFeaturedTours();

            return view('index::index', [
                'topTestimonials' => $topTestimonials,
                'latestPosts' => $latestPosts,
                'featuredTours' => $featuredTours,
            ]);
        }

        $page = Page::where('slug', $slug)->first();

        if ($page === null) {
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
