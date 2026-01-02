<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;

class SiteHotelController extends SiteController
{
    public function index(): View
    {
        return view('hotel::index');
    }
}
