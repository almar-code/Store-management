<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('p_id');
            $table->text('comment_ar');
            $table->text('comment_en');
            $table->decimal('stars', 2, 1)->default(5.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_comments');
    }
};
