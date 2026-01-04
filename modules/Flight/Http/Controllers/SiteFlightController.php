<?php

namespace Modules\Flight\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteFlightController extends SiteController
{
    public function index(): View
    {
        return view('flight::index');
    }

    public function search(): View
    {
        return view('flight::search-flight');
    }
}
