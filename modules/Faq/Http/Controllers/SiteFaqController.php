<?php

namespace Modules\Faq\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteFaqController extends SiteController
{
    public function index(): View
    {
        return view('faq::index');
    }
}
