<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutSession extends Model
{
    protected $primaryKey = 'checkout_id';

    protected $fillable = [
        'customer_id',
        'address_id',
        'amount',
        'currency',
        'cart_snapshot',
        'status',
        'payment_intent_id',
        'order_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'cart_snapshot' => 'array',
        ];
    }
}