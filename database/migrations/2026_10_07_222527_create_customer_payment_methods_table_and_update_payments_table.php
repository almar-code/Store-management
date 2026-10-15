<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Customer Payment Methods
        |--------------------------------------------------------------------------
        | This table stores the customer's saved payment methods.
        | We DO NOT store card number or CVV here.
        */
        Schema::create('customer_payment_methods', function (Blueprint $table) {

            $table->bigIncrements('customer_payment_method_id');

            $table->unsignedBigInteger('customer_id');

            // Payment provider, e.g. stripe
            $table->string('provider')->default('stripe');

            // Stripe Customer ID
            $table->string('provider_customer_id')->nullable();

            // Stripe Payment Method ID
            $table->string('provider_payment_method_id')->unique();

            // Payment method type
            $table->string('type')->default('card');

            // Card information safe to display
            $table->string('brand')->nullable();

            $table->string('last4', 4)->nullable();

            $table->unsignedTinyInteger('expiry_month')->nullable();

            $table->unsignedSmallInteger('expiry_year')->nullable();

            // Default payment method for the customer
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */
            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->cascadeOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | Update Payments Table
        |--------------------------------------------------------------------------
        */

        Schema::table('payments', function (Blueprint $table) {

            // Payment provider, e.g. stripe
            $table->string('provider')
                ->nullable()
                ->after('transaction_id');

            // Stripe PaymentIntent ID
            $table->string('provider_payment_intent_id')
                ->nullable()
                ->unique()
                ->after('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Remove fields from payments
        |--------------------------------------------------------------------------
        */

        Schema::table('payments', function (Blueprint $table) {

            $table->dropUnique([
                'provider_payment_intent_id'
            ]);

            $table->dropColumn([
                'provider',
                'provider_payment_intent_id'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Remove customer payment methods table
        |--------------------------------------------------------------------------
        */

        Schema::dropIfExists('customer_payment_methods');
    }
};