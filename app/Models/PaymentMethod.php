<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $primaryKey = 'method_id'; // هذا المفتاح الصحيح
    protected $fillable = ['method_name', 'is_active'];
}
