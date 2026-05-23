<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('video_stats', function (Blueprint $table) {
        $table->id();
        
        // 1. قمنا بإنشاء الحقل كـ bigInteger غير سالب ليتطابق مع المفاتيح الأساسية الكبيرة
        $table->unsignedBigInteger('video_id')->unique();
        
        // 2. قمنا بربط العلاقة يدوياً وتحديد أن المفتاح في جدول videos اسمه 'video_id'
        $table->foreign('video_id')
              ->references('video_id') // 👈 تأكد أن هذا هو اسم المعرف في جدول الفيديوهات
              ->on('videos')
              ->onDelete('cascade');
        
        $table->unsignedInteger('likes_count')->default(0);
        $table->unsignedInteger('saves_count')->default(0);
        $table->unsignedInteger('shares_count')->default(0);
        
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('video_stats');
    }
};