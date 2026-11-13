<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupRestore extends Model
{
    protected $fillable = [
        'user_id', 'backup_file', 'safety_file', 'undo_of',
        'status', 'step', 'message', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // اسم ولون كل حالة
    public const STATUSES = [
        'pending' => ['label' => 'بالانتظار',      'color' => '#6c757d', 'bg' => '#f1f3f5', 'icon' => 'bi-hourglass'],
        'running' => ['label' => 'جاري التنفيذ',   'color' => '#e8a33d', 'bg' => '#fff3e0', 'icon' => 'bi-arrow-repeat'],
        'success' => ['label' => 'تم بنجاح',       'color' => '#008870', 'bg' => '#e6f5f2', 'icon' => 'bi-check-circle'],
        'failed'  => ['label' => 'فشل',            'color' => '#d63031', 'bg' => '#fdecec', 'icon' => 'bi-x-circle'],
        'undone'  => ['label' => 'تم التراجع عنه', 'color' => '#1a4372', 'bg' => '#e8eef6', 'icon' => 'bi-arrow-counterclockwise'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function statusInfo(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES['pending'];
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'running']);
    }

    // اسم المستخدم للعرض
    public function userName(): string
    {
        return $this->user?->full_name ?: ($this->user?->username ?: 'غير معروف');
    }
}
