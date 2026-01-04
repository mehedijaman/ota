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

    public function search(): View
    {
        return view('hotel::search-hotel');
    }

    public function show(int $id): View
    {
        return view('hotel::show-hotel', ['id' => $id]);
    }
}
