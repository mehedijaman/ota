<?php

namespace Modules\Index\Http\Controllers;

use Modules\Page\Models\Page;
use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index($slug = null)
    {
        if (is_null($slug)) {
            return view('index::index');
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
