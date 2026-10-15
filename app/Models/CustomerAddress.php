<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
   
    protected $table = 'customer_addresses';
    protected $primaryKey = 'address_id';

    protected $fillable = [
        'customer_id',
        'title',
        'country',
        'city',
        'street',
        'building',
        'postal_code',
        'is_default',
    ];

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }
}
