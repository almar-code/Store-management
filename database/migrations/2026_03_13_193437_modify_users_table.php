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
         Schema::table('users', function (Blueprint $table) {

            // تغيير اسم id إلى user_id
            $table->renameColumn('id', 'user_id');

            // تغيير name إلى username
            $table->renameColumn('name', 'username');

            // تغيير password إلى password_hash
            $table->renameColumn('password', 'password_hash');

            // إضافة الأعمدة الجديدة
            $table->string('full_name')->after('username');
            $table->text('address')->nullable()->after('email');

            // حذف الأعمدة غير المطلوبة
            $table->dropColumn('remember_token');
            $table->dropColumn('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->renameColumn('user_id', 'id');
            $table->renameColumn('username', 'name');
            $table->renameColumn('password_hash', 'password');

            $table->dropColumn('full_name');
            $table->dropColumn('address');

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });
    }
};
