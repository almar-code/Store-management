<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_performance', function (Blueprint $table) {
            $table->id('performance_id');

            // المنتج ويوم التحليل
            $table->unsignedBigInteger('p_id');
            $table->date('analysis_date');

            // نتيجة التحليل
            $table->string('status', 20);                 // high, normal, stagnant, pending
            $table->decimal('score', 5, 2)->default(0);   // نسبة الأداء من 100
            $table->unsignedTinyInteger('cluster')->nullable(); // مجموعة K-Means
            $table->string('diagnosis', 50)->nullable();  // رمز سبب الحالة

            // نسخة من العدادات في يوم التحليل
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('cart_adds')->default(0);
            $table->unsignedInteger('favorites')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('sales')->default(0);

            // المؤشرات المحسوبة
            $table->json('indicators')->nullable();

            $table->timestamps();

            // صف واحد لكل منتج في اليوم
            $table->unique(['p_id', 'analysis_date']);
            $table->index('analysis_date');

            $table->foreign('p_id')
                  ->references('p_id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_performance');
    }
};