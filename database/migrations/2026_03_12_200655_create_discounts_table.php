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
        Schema::create('discounts', function (Blueprint $table) {

            $table->bigIncrements('discount_id');

            $table->unsignedBigInteger('p_id'); // رقم المنتج

            $table->integer('discount_perce'); // نسبة الخصم

            $table->integer('duration'); // مدة الخصم (مثلاً بالأيام)

            $table->timestamps();

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
        Schema::dropIfExists('discounts');
    }
};
