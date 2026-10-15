<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPaymentMethod extends Model
{
    protected $table = 'customer_payment_methods';

    protected $primaryKey = 'customer_payment_method_id';

    protected $fillable = [
        'customer_id',
        'provider',
        'provider_customer_id',
        'provider_payment_method_id',
        'type',
        'brand',
        'last4',
        'expiry_month',
        'expiry_year',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id',
            'customer_id'
        );
    }
}