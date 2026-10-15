<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();

            $table->foreign('color_id')
                ->references('color_id')
                ->on('colors')
                ->nullOnDelete();

            $table->foreign('size_id')
                ->references('size_id')
                ->on('sizes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropForeign(['size_id']);

            $table->dropColumn(['color_id', 'size_id']);
        });
    }
};