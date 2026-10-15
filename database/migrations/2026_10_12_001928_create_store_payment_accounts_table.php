
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_payment_accounts', function (Blueprint $table) {
            $table->id('store_payment_account_id');

            // bank أو wallet
            $table->string('account_type', 20);

            $table->string('provider_name');
            $table->string('account_holder_name')->nullable();
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->text('instructions')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['account_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_payment_accounts');
    }
};
