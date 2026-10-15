<?php

namespace App\Services;

use App\Models\Customer;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(
            config('services.stripe.secret')
        );
    }

    /**
     * Create or retrieve the Stripe Customer
     */
    public function getOrCreateCustomer(Customer $customer)
    {
        if (!empty($customer->stripe_customer_id)) {

            return $this->stripe->customers->retrieve(
                $customer->stripe_customer_id
            );
        }

        $stripeCustomer = $this->stripe->customers->create([
            'name' => $customer->name,
            'email' => $customer->email,

            'metadata' => [
                'customer_id' => (string) $customer->customer_id,
                'supabase_id' => (string) $customer->supabase_id,
            ],
        ]);

        $customer->stripe_customer_id = $stripeCustomer->id;
        $customer->save();

        return $stripeCustomer;
    }

    /**
     * Create SetupIntent for saving a card.
     */
    public function createSetupIntent(Customer $customer)
    {
        $stripeCustomer = $this->getOrCreateCustomer($customer);

        return $this->stripe->setupIntents->create([
            'customer' => $stripeCustomer->id,

            'automatic_payment_methods' => [
            'enabled' => true,
        ],
            'usage' => 'off_session',

            'metadata' => [
                'customer_id' => (string) $customer->customer_id,
            ],
        ]);
    }

    /**
     * Create an ephemeral key for Flutter Stripe.
     */
    public function createEphemeralKey(Customer $customer)
    {
        $stripeCustomer = $this->getOrCreateCustomer($customer);

        return $this->stripe->ephemeralKeys->create(
            [
                'customer' => $stripeCustomer->id,
            ],
            [
                'stripe_version' => config(
                    'services.stripe.api_version'
                ),
            ]
        );
    }

    /**
     * Retrieve a SetupIntent.
     */
    public function retrieveSetupIntent(string $setupIntentId)
    {
        return $this->stripe->setupIntents->retrieve(
            $setupIntentId,
            [
                'expand' => [
                    'payment_method'
                ]
            ]
        );
    }

    /**
     * Create PaymentIntent.
     */
    public function createPaymentIntent(
        Customer $customer,
        int $amount,
        string $currency,
        int $orderId,
        int $paymentId
    ) {
        $stripeCustomer = $this->getOrCreateCustomer($customer);

        return $this->stripe->paymentIntents->create(
            [
                'amount' => $amount,

                'currency' => strtolower($currency),

                'customer' => $stripeCustomer->id,

                'automatic_payment_methods' => [
            'enabled' => true,
        ],

                'metadata' => [
                    'order_id' => (string) $orderId,
                    'payment_id' => (string) $paymentId,
                    'customer_id' => (string) $customer->customer_id,
                ],
            ],
            [
                'idempotency_key' => 'order_' . $orderId . '_payment',
            ]
        );
    }

    /**
     * Detach saved payment method from Stripe.
     */
    public function detachPaymentMethod(string $paymentMethodId)
    {
        return $this->stripe
            ->paymentMethods
            ->detach($paymentMethodId);
    }
    public function createCheckoutPaymentIntent(
    \App\Models\Customer $customer,
    int $amountInMinorUnits,
    string $currency,
    int $checkoutId,
    string $stripePaymentMethodId
) {
    $stripeCustomer = $this->getOrCreateCustomer($customer);

    return $this->stripe->paymentIntents->create(
        [
            'amount' => $amountInMinorUnits,
            'currency' => strtolower($currency),
            'customer' => $stripeCustomer->id,
            'payment_method' => $stripePaymentMethodId,

            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],

            'metadata' => [
                'checkout_id' => (string) $checkoutId,
                'customer_id' => (string) $customer->customer_id,
            ],
        ],
        [
            'idempotency_key' => 'checkout_' . $checkoutId,
        ]
    );
}
}