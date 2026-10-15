
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('store_payment_account_id')
                ->nullable();

            // نسخة من بيانات الحساب وقت إنشاء الطلب
            $table->string('store_account_name')->nullable();
            $table->string('store_account_number')->nullable();

            // بيانات يرسلها العميل
            $table->string('customer_bank_name')->nullable();
            $table->string('customer_transaction_reference')->nullable();

            // pending / confirmed / rejected
            $table->string('verification_status')
                ->default('pending');

            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_note')->nullable();

            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['verification_status']);

            $table->dropColumn([
                'store_payment_account_id',
                'store_account_name',
                'store_account_number',
                'customer_bank_name',
                'customer_transaction_reference',
                'verification_status',
                'verified_by',
                'verified_at',
                'verification_note',
            ]);
        });
    }
};
