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
        Schema::create('product_visual_features', function (Blueprint $table) {
            $table->bigIncrements('feature_id');
            
            // الربط المباشر بجدول الألوان (المخصص للون المحدد للمنتج)
            $table->unsignedBigInteger('color_id');
            
            $table->string('image_hash')->nullable()->index();
            
            // اللون السائد المستخرج من Google Vision (HEX code)
            $table->string('dominant_color')->nullable();
            
            // الوسوم والخصائص البصرية المستخرجة للون هذا المنتج (مفصولة بفواصل)
            $table->text('image_labels')->nullable();
            
            // تفاصيل البيانات كاملة المرجعة من Google كـ JSON مرن
            $table->json('image_features_json')->nullable();
            
            $table->timestamps();

            // المفتاح الأجنبي المربوط بجدول colors مع الحذف المتسلسل
            $table->foreign('color_id')->references('color_id')->on('colors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_visual_features');
    }
};