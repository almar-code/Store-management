<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // وقت آخر تذكير أُرسل للسلة
        Schema::table('carts', function (Blueprint $table) {
            $table->timestamp('abandoned_reminded_at')->nullable()->after('customer_id');
        });

        // إشعارات العملاء داخل التطبيق
        Schema::create('customer_notifications', function (Blueprint $table) {
            $table->bigIncrements('notification_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('type', 50);
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('body_ar');
            $table->text('body_en');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->index(['customer_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_notifications');

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('abandoned_reminded_at');
        });
    }
};
