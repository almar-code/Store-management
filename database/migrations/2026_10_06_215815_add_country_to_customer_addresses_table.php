<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            // إضافة حقل الدولة بعد حقل المسمى الوظيفي/العنوان (title)
            $table->string('country')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            // إزالة الحقل في حال التراجع عن الميجريشن
            $table->dropColumn('country');
        });
    }
};