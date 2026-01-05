<?php

namespace Modules\Employee\Filters;

use Closure;

class DesignationFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('designation') && request('designation') !== '' && request('designation') !== 'null' && ! is_null(request('designation'))) {
            $query->where('designation_id', request('designation'));
        }

        return $next($query);
    }
}
