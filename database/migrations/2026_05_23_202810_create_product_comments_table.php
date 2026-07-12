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
        Schema::create('product_comments', function (Blueprint $table) {
            $table->bigIncrements('comment_id'); // المعرف الأساسي للتعليق

            // المفاتيح الخارجية للربط بالعميل والمنتج
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('p_id');

            // حقول التعليق باللغتين
            $table->text('comment_ar')->nullable(); // النص العربي (nullable في حال قيام العميل بالتقييم بالنجوم فقط بدون كتابة)
            $table->text('comment_en')->nullable(); // النص الإنجليزي

            // حقل عدد النجوم (التقييم)
            // نستخدم tinyInteger لأنه يستهلك مساحة أقل في قاعدة البيانات وممتاز للأرقام الصغيرة (من 1 إلى 5)
            $table->tinyInteger('stars')->unsigned()->default(5); 

            $table->timestamps(); // لتسجيل وقت وتاريخ كتابة التعليق

            // العلاقات والحذف المتتالي (Cascade)
            $table->foreign('customer_id')->references('customer_id')->on('customers')->cascadeOnDelete();
            $table->foreign('p_id')->references('p_id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_comments');
    }
};