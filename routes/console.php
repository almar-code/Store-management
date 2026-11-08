<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('ads:deactivate-expired')->everyMinute();
// تحليل أداء المنتجات يومياً الساعة 3 فجراً بتوقيت اليمن
Schedule::command('products:analyze')->timezone('Asia/Aden')->dailyAt('03:00');