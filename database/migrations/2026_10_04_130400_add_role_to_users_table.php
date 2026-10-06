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
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل enum لتحديد نوع المستخدم مع قيمة افتراضية 'user'
            $table->enum('role', ['admin', 'user'])->default('user')->after('password_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف الحقل عند التراجع عن الميجريشن
            $table->dropColumn('role');
        });
    }
};