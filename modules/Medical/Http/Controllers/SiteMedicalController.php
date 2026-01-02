<?php

namespace Modules\Medical\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteMedicalController extends SiteController
{
    public function index(): View
    {
        return view('medical::index');
    }
}
