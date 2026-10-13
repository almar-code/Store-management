
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->string('type', 20)
                ->default('image')
                ->after('AdsImage');

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->after('type');

            $table->foreign('product_id')
                ->references('p_id')
                ->on('products')
                ->nullOnDelete();
        });

        // تصنيف الإعلانات القديمة التي تحتوي على رابط.
        DB::table('advertisement')
            ->whereNotNull('AdsLink')
            ->where('AdsLink', '!=', '')
            ->update(['type' => 'link']);
    }

    public function down(): void
    {
        Schema::table('advertisement', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'type']);
        });
    }
};
