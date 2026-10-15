<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            return response()->json([
                'message' => 'Stripe webhook secret is not configured.',
            ], 500);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                $secret
            );
        } catch (\UnexpectedValueException |
                 SignatureVerificationException $e) {
            return response()->json([
                'message' => 'Invalid webhook signature.',
            ], 400);
        }

        try {
            if ($event->type === 'payment_intent.succeeded') {
                $intent = $event->data->object;

                $this->completeCheckout($intent);
            }

            if ($event->type === 'payment_intent.payment_failed') {
                $intent = $event->data->object;

                CheckoutSession::where(
                    'payment_intent_id',
                    $intent->id
                )
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);
            }

            return response()->json(['received' => true]);
        } catch (Throwable $e) {
            report($e);

            /*
             * 500 يجعل Stripe يعيد محاولة إرسال الحدث.
             */
            return response()->json([
                'message' => 'Webhook processing failed.',
            ], 500);
        }
    }

    private function completeCheckout(object $intent): void
    {
        DB::transaction(function () use ($intent) {
            $checkoutId = $intent->metadata->checkout_id ?? null;

            if (!$checkoutId) {
                throw new \RuntimeException(
                    'Checkout ID is missing from Stripe metadata.'
                );
            }

            $checkout = CheckoutSession::where(
                'checkout_id',
                $checkoutId
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Stripe قد يعيد إرسال الحدث أكثر من مرة.
             * لا ننشئ الطلب مرتين.
             */
            if ($checkout->status === 'completed') {
                return;
            }

            if ($checkout->status !== 'pending') {
                throw new \RuntimeException(
                    'Checkout is not in pending status.'
                );
            }

            if ($checkout->payment_intent_id !== $intent->id) {
                throw new \RuntimeException(
                    'PaymentIntent does not match the checkout.'
                );
            }

            $expectedAmount = (int) round(
                (float) $checkout->amount * 100
            );

            if (
                (int) $intent->amount_received !== $expectedAmount
                || strtolower($intent->currency)
                    !== strtolower($checkout->currency)
            ) {
                throw new \RuntimeException(
                    'The Stripe payment amount or currency does not match.'
                );
            }

            $now = now();

            /*
             * إنشاء الطلب النهائي بعد تأكيد الدفع.
             */
            $orderId = DB::table('orders')->insertGetId([
                'customer_id' => $checkout->customer_id,
                'address_id' => $checkout->address_id,
                'total_price' => $checkout->amount,
                'status' => 'pending',
                'payment_status' => 'paid',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            /*
             * نقل عناصر النسخة المحفوظة إلى order_items.
             */
            foreach ($checkout->cart_snapshot as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'p_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['unit_price'],
                    'color_id' => $item['color_id'] ?? null,
                    'size_id' => $item['size_id'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            /*
             * احصل على طريقة الدفع المحلية Card.
             */
            $methodId = DB::table('payment_methods')
                ->where('method_name', 'Card')
                ->value('method_id');

            if (!$methodId) {
                $methodId = DB::table('payment_methods')
                    ->insertGetId([
                        'method_name' => 'Card',
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
            }

            /*
             * حفظ عملية الدفع في جدول payments.
             */
            DB::table('payments')->insert([
                'order_id' => $orderId,
                'method_id' => $methodId,
                'amount' => $checkout->amount,
                'transaction_id' => $intent->id,
                'status' => 'completed',
                'provider' => 'stripe',
                'provider_payment_intent_id' => $intent->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            /*
             * احذف فقط عناصر السلة التي كانت ضمن هذه العملية.
             * العناصر التي أضيفت بعد بدء الدفع تبقى في السلة.
             */
            $snapshot = $checkout->cart_snapshot;

            $cartItemIds = collect($snapshot)
                ->pluck('cart_item_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $cartIds = collect($snapshot)
                ->pluck('cart_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            if (!empty($cartItemIds) && !empty($cartIds)) {
                DB::table('cart_items')
                    ->whereIn('cart_item_id', $cartItemIds)
                    ->whereIn('cart_id', $cartIds)
                    ->delete();
            }

            $checkout->update([
                'status' => 'completed',
                'order_id' => $orderId,
            ]);
        });
    }
}