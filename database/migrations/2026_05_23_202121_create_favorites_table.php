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
        Schema::create('favorites', function (Blueprint $table) {
            $table->bigIncrements('favorite_id'); // المعرف الأساسي للجدول

            // المفاتيح الخارجية المربوطة بجداولك بدقة
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('p_id');
            
            $table->timestamps(); // اختياري: لمعرفة تاريخ إضافة المنتج للمفضلة

            // 🌟 الربط والعلاقات مع الحذف المتتالي (Cascade On Delete)
            // إذا حُذف العميل أو حُذف المنتج، تُحذف المفضلة الخاصة به تلقائياً من قاعدة البيانات
            $table->foreign('customer_id')->references('customer_id')->on('customers')->cascadeOnDelete();
            $table->foreign('p_id')->references('p_id')->on('products')->cascadeOnDelete();

            // 🌟 اللمسة الاحترافية (Composite Unique Index): 
            // هذا السطر يمنع قاعدة البيانات نهائياً من تكرار نفس المنتج لنفس العميل في المفضلة
            $table->unique(['customer_id', 'p_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};