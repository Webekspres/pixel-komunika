<?php

namespace App\Domains\BackgroundJobs;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class WorkdayCalculator
{
    public function addWorkdays(CarbonInterface $from, int $workdays): CarbonInterface
    {
        $date = $from->copy()->startOfDay();
        $added = 0;

        while ($added < $workdays) {
            $date->addDay();
            if ($this->isWorkday($date)) {
                $added++;
            }
        }

        return $date;
    }

    public function isWorkday(CarbonInterface $date): bool
    {
        if (in_array($date->dayOfWeekIso, [6, 7], true)) {
            return false; // Sat/Sun
        }

        $holidays = config('store.holidays', []);

        return ! in_array($date->toDateString(), $holidays, true);
    }

    public function workdaysSince(CarbonInterface $from, ?CarbonInterface $to = null): int
    {
        $to ??= Carbon::now();
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        $count = 0;

        while ($cursor->lt($end)) {
            $cursor->addDay();
            if ($this->isWorkday($cursor)) {
                $count++;
            }
        }

        return $count;
    }
}
