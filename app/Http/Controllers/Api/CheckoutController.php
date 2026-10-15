<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CheckoutSession;
use App\Models\Customer;
use App\Models\CustomerPaymentMethod;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer', 'min:1'],
            'address_id' => ['required', 'integer', 'min:1'],
            'customer_payment_method_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid checkout information.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $customer = Customer::where(
                'customer_id',
                $request->customer_id
            )->firstOrFail();

            $address = DB::table('customer_addresses')
                ->where('address_id', $request->address_id)
                ->where('customer_id', $customer->customer_id)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected address does not belong to this customer.',
                ], 422);
            }

            $savedCard = CustomerPaymentMethod::where(
                'customer_payment_method_id',
                $request->customer_payment_method_id
            )
                ->where('customer_id', $customer->customer_id)
                ->where('provider', 'stripe')
                ->firstOrFail();

            $cart = Cart::where(
                'customer_id',
                $customer->customer_id
            )
                ->with('items')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty.',
                ], 422);
            }

            $snapshot = [];
            $total = 0.0;

            foreach ($cart->items as $item) {
                $product = DB::table('products')
                    ->where('p_id', $item->product_id)
                    ->first();

                if (!$product) {
                    throw new \RuntimeException(
                        'A product in the cart no longer exists.'
                    );
                }

                $unitPrice = (float) $product->p_price;
                $quantity = (int) $item->quantity;

                if ($quantity < 1 || $unitPrice < 0) {
                    throw new \RuntimeException(
                        'Invalid product quantity or price.'
                    );
                }

                $lineTotal = round($unitPrice * $quantity, 2);
                $total = round($total + $lineTotal, 2);

                $snapshot[] = [
                    'cart_item_id' => (int) $item->cart_item_id,
                    'cart_id' => (int) $cart->cart_id,
                    'product_id' => (int) $item->product_id,
                    'color_id' => $item->color_id
                        ? (int) $item->color_id : null,
                    'size_id' => $item->size_id
                        ? (int) $item->size_id : null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
            }

            if ($total <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'The checkout amount must be greater than zero.',
                ], 422);
            }

            $currency = strtolower(
                config('services.stripe.currency', 'sar')
            );

            /*
             * إنشاء جلسة مؤقتة أولًا.
             * لن ننشئ الطلب النهائي هنا.
             */
            $checkout = CheckoutSession::create([
                'customer_id' => $customer->customer_id,
                'address_id' => $address->address_id,
                'amount' => $total,
                'currency' => $currency,
                'cart_snapshot' => $snapshot,
                'status' => 'pending',
            ]);

            $amountInMinorUnits = (int) round($total * 100);

            $intent = $this->stripeService
                ->createCheckoutPaymentIntent(
                    $customer,
                    $amountInMinorUnits,
                    $currency,
                    $checkout->checkout_id,
                    $savedCard->provider_payment_method_id
                );

            $checkout->update([
                'payment_intent_id' => $intent->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout started.',
                'data' => [
                    'checkout_id' => $checkout->checkout_id,
                    'client_secret' => $intent->client_secret,
                    'stripe_payment_method_id' =>
                        $savedCard->provider_payment_method_id,
                    'amount' => (float) $checkout->amount,
                    'currency' => $checkout->currency,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to start checkout.',
            ], 500);
        }
    }
}