<?php

namespace Modules\Employee\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class DepartmentFilter
{
    public function handle(Builder $query, Closure $next): Builder
    {
        if (request()->filled('department')) {
            $query->where('department_id', request('department'));
        }

        return $next($query);
    }
}
