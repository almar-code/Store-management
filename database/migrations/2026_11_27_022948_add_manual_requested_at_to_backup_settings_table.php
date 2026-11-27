<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            // وقت طلب نسخة يدوية من لوحة التحكم
            $table->timestamp('manual_requested_at')->nullable()->after('last_backup_message');
        });
    }

    public function down(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->dropColumn('manual_requested_at');
        });
    }
};