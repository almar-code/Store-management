<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\Customer;
use App\Models\CustomerNotification;
use Illuminate\Support\Facades\DB;

class AdNotifier
{
    // إرسال إشعار الإعلان العام مرة واحدة لكل العملاء
    public function notifyIfNeeded(Advertisement $ad): bool
    {
        if ($ad->type !== 'general' || !$ad->is_active || $ad->notified_at !== null) {
            return false;
        }

        DB::transaction(function () use ($ad) {
            // نسجل الإرسال أولا لمنع التكرار
            $ad->forceFill(['notified_at' => now()])->save();

            $data = json_encode(['ads_id' => $ad->ads_id, 'ads_name' => $ad->AdsName], JSON_UNESCAPED_UNICODE);

            Customer::query()->select('customer_id')->chunkById(500, function ($customers) use ($ad, $data) {
                $now = now();
                $rows = $customers->map(fn ($c) => [
                    'customer_id' => $c->customer_id,
                    'type' => 'general_ad',
                    'title_ar' => "عرض جديد: {$ad->AdsName}",
                    'title_en' => "New offer: {$ad->AdsName}",
                    'body_ar' => 'تصفح منتجات العرض الآن',
                    'body_en' => 'Browse the offer products now',
                    'data' => $data,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                CustomerNotification::insert($rows);
            }, 'customer_id');
        });

        return true;
    }
}
