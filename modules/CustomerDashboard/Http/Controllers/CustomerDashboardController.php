<?php

namespace Modules\CustomerDashboard\Http\Controllers;

use Inertia\Inertia;
use Modules\Support\Http\Controllers\AppController;

class CustomerDashboardController extends AppController
{
    /**
     * Display the customer dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('CustomerDashboard/CustomerDashboard');
    }
}
