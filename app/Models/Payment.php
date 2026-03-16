<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    public function method() {
        return $this->belongsTo(PaymentMethod::class, 'method_id', 'method_id');
        // 'method_id' الأول = عمود جدول Payment
        // 'method_id' الثاني = primary key في جدول PaymentMethod
    }
}