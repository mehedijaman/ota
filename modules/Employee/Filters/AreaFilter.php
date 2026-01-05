<?php

namespace Modules\Employee\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class AreaFilter
{
    public function handle(Builder $query, Closure $next): Builder
    {
        if (request()->filled('area')) {
            $query->where('area_id', request('area'));
        }

        return $next($query);
    }
}
