<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_restores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            // النسخة المسترجعة
            $table->string('backup_file');
            // نسخة الأمان المأخوذة قبل الاسترجاع
            $table->string('safety_file')->nullable();
            // رقم عملية الاسترجاع اللي تم التراجع عنها
            $table->unsignedBigInteger('undo_of')->nullable();
            // pending | running | success | failed | undone
            $table->string('status', 20)->default('pending');
            // المرحلة الحالية من 1 إلى 5
            $table->unsignedTinyInteger('step')->default(0);
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_restores');
    }
};
