<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            // جعل حقل الرابط يقبل القيمة الفارغة (NULL)
            $table->string('AdsLink')->nullable()->change();
            
            // التأكد من أن حقل رقم المنتج يقبل القيمة الفارغة (NULL) أيضاً
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->string('AdsLink')->nullable(false)->change();
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
