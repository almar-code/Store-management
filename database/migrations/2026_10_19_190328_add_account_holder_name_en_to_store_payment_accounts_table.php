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
        Schema::table('store_payment_accounts', function (Blueprint $table) {
            // إضافة حقل اسم المزود بالإنجليزية بحيث يقبل القيمة الفارغة ويأتي بعد حقل provider_name
            $table->string('provider_name_en')->nullable()->after('provider_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_payment_accounts', function (Blueprint $table) {
            // حذف الحقل في حال التراجع عن المايجريشن
            $table->dropColumn('provider_name_en');
        });
    }
};