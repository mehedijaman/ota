<?php

namespace App\Filters;

use Closure;

class StatusFilter
{
    public function handle($query, Closure $next)
    {
        $active = request('active');

        if ($active === null || $active === '') {
            // Default: always active
            $query->where('is_active', true);
        } else {
            // Respect explicit request parameter
            $query->where('is_active', filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        return $next($query);
    }
}
