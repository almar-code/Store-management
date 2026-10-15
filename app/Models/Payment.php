<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    protected $fillable = [
    'order_id',
    'method_id',
    'amount',
    'transaction_id',
    'provider',
    'provider_payment_intent_id',
    'status',
];
protected $casts = [
    'amount' => 'decimal:2',
];

    public function method() {
        return $this->belongsTo(PaymentMethod::class, 'method_id', 'method_id');
        // 'method_id' الأول = عمود جدول Payment
        // 'method_id' الثاني = primary key في جدول PaymentMethod
    }
}