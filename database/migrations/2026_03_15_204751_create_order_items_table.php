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
       Schema::create('order_items', function (Blueprint $table) {
    $table->bigIncrements('order_item_id');

    $table->unsignedBigInteger('order_id');
    $table->unsignedBigInteger('p_id');

    $table->integer('quantity');
    $table->decimal('price', 8, 2);

    $table->timestamps();

    $table->foreign('order_id')
        ->references('order_id')
        ->on('orders')
        ->cascadeOnDelete();

    $table->foreign('p_id')
        ->references('p_id')
        ->on('products')
        ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
