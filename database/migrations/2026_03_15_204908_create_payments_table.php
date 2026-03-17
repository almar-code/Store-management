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
        Schema::create('payments', function (Blueprint $table) {
    $table->bigIncrements('payment_id');

    $table->unsignedBigInteger('order_id');
    $table->unsignedBigInteger('method_id');

    $table->decimal('amount', 10, 2);
    $table->string('transaction_id')->nullable();
    $table->string('status')->default('pending');

    $table->timestamps();

    $table->foreign('order_id')
        ->references('order_id')
        ->on('orders')
        ->cascadeOnDelete();

    $table->foreign('method_id')
        ->references('method_id')
        ->on('payment_methods')
        ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
