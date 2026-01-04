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

    public function search(): View
    {
        return view('tour::search-tour');
    }

    public function show(int $id): View
    {
        return view('tour::show-tour', ['id' => $id]);
    }
}
