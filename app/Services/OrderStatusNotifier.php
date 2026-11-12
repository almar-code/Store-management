<?php

namespace App\Services;

use App\Mail\OrderCompletedMail;
use App\Models\CustomerNotification;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderStatusNotifier
{
    public function __construct(
        private WhatsAppService $whatsApp
    ) {}

    public function notify(Order $order): void
    {
        $customer = $order->customer;
        if (!$customer) {
            return;
        }

        // إشعار التطبيق لكل الحالات
        CustomerNotification::create([
            'customer_id' => $customer->customer_id,
            'type' => 'order_status',
            'title_ar' => 'تحديث حالة الطلب',
            'title_en' => 'Order status update',
            'body_ar' => "طلبك رقم #{$order->order_id} أصبح الآن: {$order->statusLabel('ar')}",
            'body_en' => "Your order #{$order->order_id} is now: {$order->statusLabel('en')}",
            'data' => ['order_id' => $order->order_id, 'status' => $order->status],
        ]);

        // الإيميل والواتساب للطلب الجاهز فقط
        if ($order->status !== 'ready') {
            return;
        }

        if (!empty($customer->email)) {
            rescue(fn () => Mail::to($customer->email)->send(new OrderCompletedMail($order)));
        }

        if (!empty($customer->phone)) {
            rescue(fn () => $this->whatsApp->sendMessage($customer->phone, $this->whatsAppText($order)));
        }
    }

        private function whatsAppText(Order $order): string
    {
        $name = $order->customer->name ?: 'عميلنا العزيز';
        $url = env('STORE_APP_URL', config('app.url'));

        return "مرحبا {$name}\n"
            . "طلبك رقم #{$order->order_id} أصبح جاهزاً.\n"
            . "سيتم تجهيزه للشحن قريباً، شكرا لتسوقك من متجر NICE.\n"
            . "{$url}\n\n"
            . "Your order #{$order->order_id} is ready. Thank you for shopping with NICE.";
    }
}