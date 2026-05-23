<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('video_comments', function (Blueprint $table) {
        $table->id(); // رقم التعليق الأساسي (تعليق تلقائي)
        
        // 1. تعريف حقل الفيديو كـ Big Integer غير سالب ليتطابق مع جدول الفيديوهات
        $table->unsignedBigInteger('video_id');
        
        // 2. تعريف حقل العميل كـ Big Integer غير سالب ليتطابق مع جدول العملاء
        $table->unsignedBigInteger('customer_id');
        
        // نص التعليق
        $table->text('comment_text');
        $table->timestamps();

        // ----------------------------------------------------
        // إعداد العلاقات والمفاتيح الأجنبية بشكل صريح ودقيق
        // ----------------------------------------------------
        
        // ربط التعليق بجدول الفيديوهات على حقل video_id
        $table->foreign('video_id')
              ->references('video_id') // اسم المفتاح في جدول videos
              ->on('videos')
              ->onDelete('cascade');

        // ربط التعليق بجدول العملاء على حقل customer_id
        $table->foreign('customer_id')
              ->references('customer_id') // اسم المفتاح في جدول customers
              ->on('customers')
              ->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('video_comments');
    }
};