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
        Schema::create('colors', function (Blueprint $table) {
           $table->bigIncrements('color_id');
           $table->string('color_name');
           $table->string('color_code')->nullable();
           $table->unsignedBigInteger('p_id');
           $table->timestamps();

           $table->foreign('p_id')->references('p_id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
