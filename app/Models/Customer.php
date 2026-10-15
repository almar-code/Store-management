<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
                ->withTimestamps(); // لكي يقوم بتحديث حقول timestamps تلقائياً عند الإضافة
}
public function paymentMethods()
{
    return $this->hasMany(
        CustomerPaymentMethod::class,
        'customer_id',
        'customer_id'
    );
}
}
