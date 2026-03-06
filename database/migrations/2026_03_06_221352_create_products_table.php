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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('p_id');
            $table->string('p_name');
            $table->text('p_description')->nullable();
            $table->decimal('p_price',8,2);
            $table->unsignedBigInteger('subcat_id');
            $table->timestamps();
            
            $table->foreign('subcat_id')->references('subcat_id')->on('subcategories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
