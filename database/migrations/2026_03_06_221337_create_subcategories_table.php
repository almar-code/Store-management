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
        Schema::create('subcategories', function (Blueprint $table) {
            $table->bigIncrements('subcat_id');
            $table->string('subcat_name');
            $table->string('subcat_image')->nullable();
            $table->unsignedBigInteger('cat_id');
            $table->timestamps();
            
            $table->foreign('cat_id')->references('cat_id')->on('categories')->cascadeOnDelete();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
