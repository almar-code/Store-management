<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_requests', function (Blueprint $table) {
            $table->bigIncrements('request_id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('report_query');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_requests');
    }
};