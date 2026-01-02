<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteTourController extends SiteController
{
    public function index(): View
    {
        return view('tour::index');
    }
}
