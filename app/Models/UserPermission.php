<?php

namespace App\Models;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;


class UserPermission extends Model
{
    
     protected $table = 'user_permissions'; // اسم الجدول
    public $timestamps = true; // إذا كان لديك created_at / updated_at
    protected $primaryKey = null; // لأنه مفتاح مركب
    public $incrementing = false;
    protected $fillable = ['user_id', 'permission_id', 'is_active'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function permission() {
        return $this->belongsTo(Permission::class, 'permission_id', 'permission_id');
    }
}
