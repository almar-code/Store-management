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
       Schema::create('customer_addresses', function (Blueprint $table) {
    $table->bigIncrements('address_id');

    $table->unsignedBigInteger('customer_id');

    $table->string('title'); 
    $table->string('city');
    $table->string('street');
    $table->string('building')->nullable();
    $table->string('postal_code')->nullable();
    $table->text('notes')->nullable();

    $table->boolean('is_default')->default(false);

    $table->timestamps();

    $table->foreign('customer_id')
        ->references('customer_id')
        ->on('customers')
        ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
