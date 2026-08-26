<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| Run daily at 07:00 WIB (UTC+7 = 00:00 UTC).
| Adjust the time to match your server timezone setting in config/app.php.
|
*/

Schedule::command('reminders:generate')
    ->dailyAt('00:00')   // 07:00 WIB if APP_TIMEZONE=UTC
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/reminders.log'));
