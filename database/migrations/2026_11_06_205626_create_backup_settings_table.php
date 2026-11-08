<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('run_time', 5)->default('02:00');
            $table->unsignedSmallInteger('keep_daily_days')->default(16);
            $table->unsignedSmallInteger('keep_weekly_weeks')->default(8);
            $table->unsignedSmallInteger('keep_monthly_months')->default(12);
            $table->unsignedSmallInteger('keep_yearly_years')->default(3);
            $table->timestamp('last_backup_at')->nullable();
            $table->string('last_backup_status', 20)->nullable(); // success | failed
            $table->text('last_backup_message')->nullable();
            $table->timestamps();
        });

        DB::table('backup_settings')->insert([
            'id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};