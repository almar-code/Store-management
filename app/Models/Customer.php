<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CustomerPaymentMethod;

class Customer extends Model
{
    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'supabase_id',
        'name',
        'email',
        'phone',
        'profile_image',
        'stripe_customer_id',
    ];

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'customer_id', 'p_id')
                    ->withTimestamps();
    }
        
    public function paymentMethods()
    {
        return $this->hasMany(
            CustomerPaymentMethod::class,
            'customer_id',
            'customer_id'
        );
    }

    public function cart(): HasOne
    {
        return $this->hasOne(
            Cart::class,
            'customer_id',
            'customer_id'
        );
    }
}