<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Zap\Models\SchedulePeriod as BaseSchedulePeriod;

class SchedulePeriod extends BaseSchedulePeriod
{
    use LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->useLogName('schedule_period')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
