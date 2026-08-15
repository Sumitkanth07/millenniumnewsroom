<?php

use App\Jobs\SendWeeklyNewsletterJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendWeeklyNewsletterJob)
    ->weeklyOn(1, '05:00')
    ->timezone('Asia/Kolkata');
