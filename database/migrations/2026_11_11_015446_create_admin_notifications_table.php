<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('user_id');

            // نوع الإشعار: new_order وغيرها لاحقاً
            $table->string('type', 50);
            $table->string('title');
            $table->text('body')->nullable();

            // رابط الصفحة عند الضغط
            $table->string('url')->nullable();

            // معرف العنصر المرتبط مثل رقم الطلب
            $table->unsignedBigInteger('related_id')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};