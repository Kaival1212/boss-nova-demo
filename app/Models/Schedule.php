<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Zap\Models\Schedule as BaseSchedule;

class Schedule extends BaseSchedule
{
    use LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->useLogName('schedule')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
