<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_sessions', function (Blueprint $table) {
            $table->bigIncrements('checkout_id');

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('address_id');

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('sar');

            $table->json('cart_snapshot');

            $table->string('status')->default('pending');

            $table->string('payment_intent_id')->nullable()->unique();

            $table->unsignedBigInteger('order_id')->nullable();

            $table->timestamps();

            $table->foreign(
                'customer_id',
                'fk_checkout_sessions_customer'
            )
                ->references('customer_id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->foreign(
                'address_id',
                'fk_checkout_sessions_address'
            )
                ->references('address_id')
                ->on('customer_addresses')
                ->restrictOnDelete();

            $table->foreign(
                'order_id',
                'fk_checkout_sessions_order'
            )
                ->references('order_id')
                ->on('orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_sessions');
    }
};