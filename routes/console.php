<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('properties:expire-featured')->everyFiveMinutes();
Schedule::command('payments:expire-stale')->everyFifteenMinutes();
Schedule::command('subscriptions:expire')->everyFiveMinutes();
