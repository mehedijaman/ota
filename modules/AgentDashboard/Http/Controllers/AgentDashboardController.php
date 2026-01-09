<?php

namespace Modules\AgentDashboard\Http\Controllers;

use Inertia\Inertia;
use Modules\Support\Http\Controllers\AppController;

class AgentDashboardController extends AppController
{
    /**
     * Display the agent dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('AgentDashboard/AgentDashboard');
    }
}
