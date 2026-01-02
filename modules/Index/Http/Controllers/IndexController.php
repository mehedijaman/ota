<?php

namespace Modules\Index\Http\Controllers;

use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index()
    {
        return view('index::index');
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
