<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stats', function (Blueprint $table) {
            $table->id('stat_id');

            // علاقة واحد لواحد مع المنتج (unique يمنع وجود صفين لنفس المنتج)
            $table->unsignedBigInteger('p_id')->unique();

            $table->foreign('p_id')
                  ->references('p_id')
                  ->on('products')
                  ->onDelete('cascade');

            // عدادات الأداء
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('cart_adds_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->unsignedInteger('positive_comments_count')->default(0);
            $table->unsignedInteger('negative_comments_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stats');
    }
};