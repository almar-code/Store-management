<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function favorites()
    {
        // العلاقة بين العميل والمنتجات المفضلة
        return $this->belongsToMany(
            Product::class,
            'favorites',
            'customer_id',
            'p_id'
        )->withTimestamps();
    }
}