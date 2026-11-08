<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $fillable = [
        'enabled', 'run_time',
        'keep_daily_days', 'keep_weekly_weeks', 'keep_monthly_months', 'keep_yearly_years',
        'last_backup_at', 'last_backup_status', 'last_backup_message',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_backup_at' => 'datetime',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}