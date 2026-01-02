<?php

namespace Modules\Visa\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteVisaController extends SiteController
{
    public function index(): View
    {
        return view('visa::index');
    }
}
