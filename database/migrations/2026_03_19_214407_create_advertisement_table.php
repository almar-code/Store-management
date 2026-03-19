<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('advertisement', function (Blueprint $table) {
            
        $table->bigIncrements('ads_id');
        $table->string('AdsName');
        $table->string('AdsImage'); // مسار الصورة أو الفيديو
        $table->string('AdsLink');
        $table->boolean('is_active');
        $table->date('expires_at');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement');
    }
};
