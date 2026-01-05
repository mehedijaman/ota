<?php

namespace Modules\Employee\Filters;

use Closure;

class EmployeeSelfFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('employee') && request('employee') !== '' && request('employee') !== 'null' && ! is_null(request('employee'))) {
            $query->where('id', request('employee'));
        }

        return $next($query);
    }
}
