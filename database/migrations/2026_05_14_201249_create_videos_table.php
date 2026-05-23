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
    Schema::create('videos', function (Blueprint $table) {
        $table->id('video_id'); // المعرف الخاص بجدول الفيديوهات
        $table->string('video_path'); // مسار الفيديو
        
        // يجب أن يكون من نوع unsignedBigInteger ليتوافق مع p_id في جدول المنتجات
        $table->unsignedBigInteger('product_id')->nullable(); 
        
        // الربط الصحيح: يشير إلى p_id وليس id
        $table->foreign('product_id')
              ->references('p_id') 
              ->on('products')
              ->onDelete('cascade');
              
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
