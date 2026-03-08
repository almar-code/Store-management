<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   

    public function up()
    {

        // جدول الأقسام
        Schema::table('categories', function (Blueprint $table) {
            $table->string('cat_name_en')->nullable()->after('cat_name');
        });

        // جدول الفئات
        Schema::table('subcategories', function (Blueprint $table) {
            $table->string('subcat_name_en')->nullable()->after('subcat_name');
        });

        // جدول المنتجات
        Schema::table('products', function (Blueprint $table) {
            $table->string('p_name_en')->nullable()->after('p_name');
            $table->text('p_description_en')->nullable()->after('p_description');
        });

        // جدول الألوان
        Schema::table('colors', function (Blueprint $table) {
            $table->string('color_name_en')->nullable()->after('color_name');
        });

    }

    public function down()
    {

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('cat_name_en');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn('subcat_name_en');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('p_name_en');
            $table->dropColumn('p_description_en');
        });

        Schema::table('colors', function (Blueprint $table) {
            $table->dropColumn('color_name_en');
        });

    }

};

