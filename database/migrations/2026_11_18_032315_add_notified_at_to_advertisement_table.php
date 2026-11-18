<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // وقت إرسال إشعار الإعلان للعملاء
    public function up(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->timestamp('notified_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->dropColumn('notified_at');
        });
    }
};
