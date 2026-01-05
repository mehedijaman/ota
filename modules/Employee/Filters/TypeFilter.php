<?php

namespace Modules\Employee\Filters;

use Closure;

class TypeFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('type') && request('type') !== '' && request('type') !== 'null' && ! is_null(request('type'))) {
            $query->where('type', request('type'));
        }

        return $next($query);
    }
}
