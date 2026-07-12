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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('cart_id'); // رقم السجل الخاص بالسلة

            $table->unsignedBigInteger('customer_id'); // رقم العميل
            $table->unsignedBigInteger('product_id');  // رقم المنتج
            $table->unsignedBigInteger('color_id')->nullable(); // رقم اللون (nullable في حال كان المنتج بدون ألوان)
            $table->unsignedBigInteger('size_id')->nullable();  // رقم المقاس (nullable في حال كان المنتج بدون مقاسات)
            
            $table->integer('quantity')->default(1); // الكمية المضافة (الافتراضي 1)

            $table->timestamps();

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('customers')
                  ->cascadeOnDelete();

            $table->foreign('product_id')
                  ->references('p_id') // يشير إلى p_id في جدول المنتجات لديك
                  ->on('products')
                  ->cascadeOnDelete();

            $table->foreign('color_id')
                  ->references('color_id')
                  ->on('colors')
                  ->cascadeOnDelete();

            $table->foreign('size_id')
                  ->references('size_id')
                  ->on('sizes')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};