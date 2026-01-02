<?php

namespace Modules\Bill\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteBillController extends SiteController
{
    public function index(): View
    {
        return view('bill::index');
    }
}
