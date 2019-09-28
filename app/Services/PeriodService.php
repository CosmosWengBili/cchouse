<?php

namespace App\Services;

use Carbon\Carbon;

class PeriodService
{
    private $monthsOf = [
        '月' => 1,
        '季' => 3,
        '半年' => 6,
        '年' => 12,
    ];

    public function everyMonth($from, $to, $callback)
    {
        return $this->every('月', $from, $to, $callback);
    }

    public function everyQuarter($from, $to, $callback)
    {
        return $this->every('季', $from, $to, $callback);
    }

    public function everyHalfYear($from, $to, $callback)
    {
        return $this->every('半年', $from, $to, $callback);
    }

    public function everyYear($from, $to, $callback)
    {
        return $this->every('年', $from, $to, $callback);
    }

    // maps the calculated dates collection
    public function every(string $period, $from, $to, $callback)
    {
        if ($period == '次') {
            return collect([$callback(Carbon::parse($from))]);
        } else if (isset($this->monthsOf[$period])) {
            return $this->getPeriods($from, $to, $this->monthsOf[$period])->map(
                $callback
            );
        } else {
            throw new \Exception('Invalid period: ' . $period);
        }
    }

    // get next date calculated from period
    public function next($date, string $period)
    {
        return Carbon::parse($date)
            ->copy()
            ->addMonthsWithoutOverflow($this->monthsOf[$period]);
    }

    /**
     * Calculates and gets the dates between given from-to dates and period.
     *
     * @param Carbon $from       start date
     * @param Carbon $to         end date
     * @param int    $months     period in months
     *
     * @return Collection
     */
    public function getPeriods($from, $to, int $months)
    {
        return collect(
            Carbon::parse($from)
                ->range($to, $months, 'month')
                ->excludeEndDate()
        )->map(function ($date, $i) use ($from, $months) {
            return Carbon::parse($from)
                ->copy()
                ->addMonthsWithoutOverflow($months * $i);
        });
    }
}
