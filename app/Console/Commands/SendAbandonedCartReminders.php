<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartMail;
use App\Models\Cart;
use App\Models\CustomerNotification;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminders extends Command
{
    protected $signature = 'carts:remind-abandoned';

    protected $description = 'تذكير العملاء بالسلات المتروكة';

    // مدة الترك قبل التذكير
    private const AFTER_DAYS = 2;

    // تجاهل السلات الأقدم من هذه المدة
    private const MAX_AGE_DAYS = 15;

    public function handle(WhatsAppService $whatsApp): int
    {
        $carts = Cart::with(['customer', 'items.product'])
            ->has('items')
            ->whereBetween('updated_at', [
                now()->subDays(self::MAX_AGE_DAYS),
                now()->subDays(self::AFTER_DAYS),
            ])
            // لم يُذكَّر بعد آخر تعديل
            ->where(fn ($q) => $q->whereNull('abandoned_reminded_at')
                ->orWhereColumn('abandoned_reminded_at', '<', 'updated_at'))
            // لم يُكمل طلبا بعد آخر تعديل
            ->whereNotExists(fn ($q) => $q->from('orders')
                ->whereColumn('orders.customer_id', 'carts.customer_id')
                ->whereColumn('orders.created_at', '>=', 'carts.updated_at'))
            ->get();

        foreach ($carts as $cart) {
            $this->remind($cart, $whatsApp);
        }

        $this->info("تم تذكير {$carts->count()} سلة");

        return self::SUCCESS;
    }

    private function remind(Cart $cart, WhatsAppService $whatsApp): void
    {
        $customer = $cart->customer;
        if (!$customer) {
            return;
        }

        // نسجل التذكير أولا بدون تغيير updated_at
        DB::table('carts')
            ->where('cart_id', $cart->cart_id)
            ->update(['abandoned_reminded_at' => now()]);

        $count = $cart->items->sum();

        // إشعار داخل التطبيق
        CustomerNotification::create([
            'customer_id' => $customer->customer_id,
            'type' => 'abandoned_cart',
            'title_ar' => 'سلتك بانتظارك',
            'title_en' => 'Your cart is waiting',
            'body_ar' => "لديك {$count} منتج في السلة، هل ترغب في إتمام الشراء؟",
            'body_en' => "You have {$count} item(s) in your cart. Would you like to complete your purchase?",
            'data' => ['cart_id' => $cart->cart_id],
        ]);

        // الإيميل
        if (!empty($customer->email)) {
            rescue(fn () => Mail::to($customer->email)->send(new AbandonedCartMail($cart)));
        }

        // الواتساب
        if (!empty($customer->phone)) {
            rescue(fn () => $whatsApp->sendMessage($customer->phone, $this->whatsAppText($customer->name)));
        }
    }

    private function whatsAppText(?string $name): string
{
    $name = $name ?: 'عميلنا العزيز';
    $url = env('STORE_APP_URL', config('app.url'));

    return "مرحبا {$name} 💚\n"
        . "لاحظنا أنك تركت بعض المنتجات في سلتك في متجر NICE.\n"
        . "هل ترغب في إتمام الشراء؟\n"
        . "You left some items in your NICE cart. Would you like to complete your purchase?\n"
        . "{$url}\n\n";
}
}
