
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisement_product', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ads_id');
            $table->unsignedBigInteger('p_id');

            $table->timestamps();

            $table->foreign('ads_id')
                ->references('ads_id')
                ->on('advertisement')
                ->cascadeOnDelete();

            $table->foreign('p_id')
                ->references('p_id')
                ->on('products')
                ->cascadeOnDelete();

            // يمنع تكرار المنتج داخل الإعلان نفسه.
            $table->unique(['ads_id', 'p_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisement_product');
    }
};
