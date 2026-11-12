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
// تذكير السلات المتروكة
Schedule::command('carts:remind-abandoned')
    ->timezone('Asia/Aden')
    ->hourly()
    ->between('09:00', '22:00');
// تحليل الألوان غير المحللة يومياً وقت قلة الضغط على Gemini
Schedule::command('visual:index-missing')
    ->timezone('Asia/Aden')
    ->dailyAt('11:00')
    ->withoutOverlapping();