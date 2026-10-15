<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * إذا لم توجد النسخة الاحتياطية بعد، انقل الجدول القديم إليها.
         * إذا كانت موجودة بالفعل، لا تحاول إعادة تسميتها.
         */
        if (!Schema::hasTable('carts_legacy')
            && Schema::hasTable('carts')) {
            Schema::rename('carts', 'carts_legacy');
        }

        if (!Schema::hasTable('carts_legacy')) {
            throw new RuntimeException(
                'The legacy carts table was not found.'
            );
        }

        /*
         * احذف أي جداول جديدة جزئية ناتجة عن المحاولة الفاشلة.
         * لا نحذف carts_legacy.
         */
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::enableForeignKeyConstraints();

        /*
         * جدول واحد لكل عميل.
         */
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('cart_id');

            $table->unsignedBigInteger('customer_id')->unique();

            $table->timestamps();

            $table->foreign(
                'customer_id',
                'fk_shopping_carts_customer'
            )
                ->references('customer_id')
                ->on('customers')
                ->cascadeOnDelete();
        });

        /*
         * كل منتج مختار يوضع في cart_items.
         */
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('cart_item_id');

            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');

            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            $table->foreign(
                'cart_id',
                'fk_cart_items_cart'
            )
                ->references('cart_id')
                ->on('carts')
                ->cascadeOnDelete();

            $table->foreign(
                'product_id',
                'fk_cart_items_product'
            )
                ->references('p_id')
                ->on('products')
                ->cascadeOnDelete();

            $table->foreign(
                'color_id',
                'fk_cart_items_color'
            )
                ->references('color_id')
                ->on('colors')
                ->nullOnDelete();

            $table->foreign(
                'size_id',
                'fk_cart_items_size'
            )
                ->references('size_id')
                ->on('sizes')
                ->nullOnDelete();
        });

        /*
         * نقل عناصر الجدول القديم إلى الهيكل الجديد.
         * نحتفظ بالجدول القديم حتى يتم التحقق من البيانات.
         */
        $legacyItems = DB::table('carts_legacy')
            ->orderBy('cart_id')
            ->get();

        $cartIdsByCustomer = [];

        foreach ($legacyItems as $oldItem) {
            $customerId = (int) $oldItem->customer_id;

            if (!isset($cartIdsByCustomer[$customerId])) {
                $existingCart = DB::table('carts')
                    ->where('customer_id', $customerId)
                    ->value('cart_id');

                if ($existingCart) {
                    $cartIdsByCustomer[$customerId] =
                        (int) $existingCart;
                } else {
                    $now = now();

                    $cartIdsByCustomer[$customerId] =
                        DB::table('carts')->insertGetId([
                            'customer_id' => $customerId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                }
            }

            DB::table('cart_items')->insert([
                'cart_id' => $cartIdsByCustomer[$customerId],
                'product_id' => $oldItem->product_id,
                'color_id' => $oldItem->color_id,
                'size_id' => $oldItem->size_id,
                'quantity' => max(1, (int) $oldItem->quantity),
                'created_at' => $oldItem->created_at ?? now(),
                'updated_at' => $oldItem->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        /*
         * نرجع إلى الجدول القديم المحفوظ بدل محاولة
         * إعادة بناء بياناته من الجداول الجديدة.
         */
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        if (Schema::hasTable('carts_legacy')) {
            Schema::rename('carts_legacy', 'carts');
        }

        Schema::enableForeignKeyConstraints();
    }
};