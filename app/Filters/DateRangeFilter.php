<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DateRangeFilter
{
    protected string $column;

    protected ?Request $request;

    public function __construct(string $column = 'created_at', ?Request $request = null)
    {
        $this->column = $column;
        $this->request = $request ?? request();
    }

    /**
     * Handle the incoming query filter.
     *
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {
        $dateFrom = $this->request->input('date_from');
        $dateTo = $this->request->input('date_to');

        if ($this->isValidDate($dateFrom)) {
            $query->whereDate($this->column, '>=', Carbon::parse($dateFrom)->toDateString());
        }

        if ($this->isValidDate($dateTo)) {
            $query->whereDate($this->column, '<=', Carbon::parse($dateTo)->toDateString());
        }

        return $next($query);
    }

    /**
     * Safely validate date format before parsing.
     */
    protected function isValidDate(?string $date): bool
    {
        if (empty($date)) {
            return false;
        }

        try {
            Carbon::parse($date);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
