<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
   
    protected $table = 'users';

    protected $primaryKey = 'user_id';
  protected $fillable = [
    'username',
    'full_name',
    'email',
    'address',
    'password_hash', // هذا موجود في قاعدة البيانات ✔
];


protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel سيعمل hash تلقائيًا
    ];
}
// app/Models/User.php

public function userPermissions() {
    // هذه العلاقة تربط المستخدم بجدول الصلاحيات الوسيط
    return $this->hasMany(UserPermission::class, 'user_id', 'user_id');
}
}
