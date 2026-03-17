<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Payment;
class Order extends Model
{
    protected $primaryKey = 'order_id';
   public function customer()
{
    return $this->belongsTo(Customer::class,'customer_id');
}

// public function address()
// {
//     return $this->belongsTo(CustomerAddress::class,'address_id');
    
// }
public function address() {
    return $this->belongsTo(CustomerAddress::class, 'address_id', 'address_id');
}
 public function items() {
        return $this->hasMany(OrderItem::class,'order_id');
    }

    public function payment() {
        return $this->hasOne(Payment::class,'order_id');
    }

}
