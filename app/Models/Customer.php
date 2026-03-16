<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
   protected $primaryKey = 'customer_id';

    protected $fillable = [
        'supabase_id',
        'name',
        'email',
        'phone'
    ];

<<<<<<< HEAD
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }
=======
    
   
>>>>>>> customer-db
}
