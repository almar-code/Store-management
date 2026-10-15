<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPaymentMethod;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    protected StripeService $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Get saved cards.
     */
    public function index(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        $cards = CustomerPaymentMethod::where(
            'customer_id',
            $request->customer_id
        )
        ->where('provider', 'stripe')
        ->where('type', 'card')
        ->orderByDesc('is_default')
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $cards,
        ]);
    }

    /**
     * Prepare Stripe for adding a card.
     */
    public function setup(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        try {

            $customer = Customer::findOrFail(
                $request->customer_id
            );

            $setupIntent = $this->stripe
                ->createSetupIntent($customer);

            $ephemeralKey = $this->stripe
                ->createEphemeralKey($customer);

            $stripeCustomer = $this->stripe
                ->getOrCreateCustomer($customer);

            return response()->json([
                'success' => true,

                'customer' => $stripeCustomer->id,

                'setupIntent' => $setupIntent->client_secret,

                'ephemeralKey' => $ephemeralKey->secret,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync the successfully saved Stripe card
     * into our local database.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'setup_intent_id' => 'required|string',
        ]);

        try {

            $customer = Customer::findOrFail(
                $request->customer_id
            );

            $stripeCustomer = $this->stripe
                ->getOrCreateCustomer($customer);

            $setupIntent = $this->stripe
                ->retrieveSetupIntent(
                    $request->setup_intent_id
                );

            /*
            |--------------------------------------------------------------------------
            | Security checks
            |--------------------------------------------------------------------------
            */

            if ($setupIntent->customer !== $stripeCustomer->id) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid customer.',
                ], 403);
            }

            if ($setupIntent->status !== 'succeeded') {

                return response()->json([
                    'success' => false,
                    'message' => 'Card setup was not successful.',
                    'status' => $setupIntent->status,
                ], 422);
            }

            $paymentMethod = $setupIntent->payment_method;

            if (!$paymentMethod || !$paymentMethod->card) {

                return response()->json([
                    'success' => false,
                    'message' => 'Card payment method not found.',
                ], 422);
            }

            $card = $paymentMethod->card;
            $savedCard = CustomerPaymentMethod::updateOrCreate(
                [
                    'provider_payment_method_id'
                        => $paymentMethod->id,
                ],
                [
                    'customer_id'
                        => $customer->customer_id,

                    'provider'
                        => 'stripe',

                    'provider_customer_id'
                        => $stripeCustomer->id,

                    'type'
                        => 'card',

                    'brand'
                        => $card->brand,

                    'last4'
                        => $card->last4,

                    'expiry_month'
                        => $card->exp_month,

                    'expiry_year'
                        => $card->exp_year,

                    'is_default'
                        => CustomerPaymentMethod::where(
                            'customer_id',
                            $customer->customer_id
                        )->doesntExist(),
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $savedCard,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a saved card.
     */
    public function destroy(
        Request $request,
        int $id
    ) {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        try {

            $card = CustomerPaymentMethod::where(
                'customer_payment_method_id',
                $id
            )
            ->where(
                'customer_id',
                $request->customer_id
            )
            ->firstOrFail();

            $this->stripe->detachPaymentMethod(
                $card->provider_payment_method_id
            );

            $card->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted successfully.',
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create PaymentIntent for an existing order.
     */
    public function createIntent(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'order_id' => 'required|integer',
            'customer_payment_method_id' => 'required|integer',
        ]);

        try {

            $customer = Customer::findOrFail(
                $request->customer_id
            );

            $order = Order::where(
                'order_id',
                $request->order_id
            )
            ->where(
                'customer_id',
                $customer->customer_id
            )
            ->firstOrFail();

            if ($order->payment_status === 'paid') {

                return response()->json([
                    'success' => false,
                    'message' => 'Order is already paid.',
                ], 422);
            }

            $savedCard = CustomerPaymentMethod::where(
                'customer_payment_method_id',
                $request->customer_payment_method_id
            )
            ->where(
                'customer_id',
                $customer->customer_id
            )
            ->where(
                'provider',
                'stripe'
            )
            ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Get the Card payment method ID
            |--------------------------------------------------------------------------
            */
            $cardMethod = PaymentMethod::firstOrCreate(
                [
                    'method_name' => 'Card',
                ],
                [
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Create local payment
            |--------------------------------------------------------------------------
            */

            $payment = Payment::where(
                'order_id',
                $order->order_id
            )
            ->where(
                'status',
                'pending'
            )
            ->latest()
            ->first();

            if (!$payment) {

                $payment = Payment::create([
                    'order_id' => $order->order_id,
                    'method_id' => $cardMethod->method_id,
                    'amount' => $order->total_price,
                    'status' => 'pending',
                    'provider' => 'stripe',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Convert amount to smallest currency unit
            |--------------------------------------------------------------------------
            */

            $amount = (int) round(
                ((float) $order->total_price) * 100
            );

            $currency = config(
                'services.stripe.currency',
                env('STRIPE_CURRENCY', 'sar')
            );

            /*
            |--------------------------------------------------------------------------
            | Create Stripe PaymentIntent
            |--------------------------------------------------------------------------
            */

            $paymentIntent = $this->stripe
                ->createPaymentIntent(
                    $customer,
                    $amount,
                    $currency,
                    $order->order_id,
                    $payment->payment_id
                );

            $payment->provider_payment_intent_id =
                $paymentIntent->id;

            $payment->transaction_id =
                $paymentIntent->id;

            $payment->save();

            return response()->json([
                'success' => true,

                'order_id' => $order->order_id,

                'payment_id' => $payment->payment_id,

                'payment_intent_id' => $paymentIntent->id,

                'client_secret' =>
                    $paymentIntent->client_secret,

                'amount' => $amount,

                'currency' => $currency,

                'stripe_payment_method_id' =>
                    $savedCard->provider_payment_method_id,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}