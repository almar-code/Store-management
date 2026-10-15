<?php

namespace App\Models;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
   
    protected $table = 'order_items';
protected $primaryKey = 'order_item_id';
 protected $fillable = [
    'order_id',
    'p_id',
    'color_id',
    'size_id',
    'quantity',
    'price',
];
    public function product() {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function color()
{
    return $this->belongsTo(Color::class, 'color_id', 'color_id');
}

public function size()
{
    return $this->belongsTo(Size::class, 'size_id', 'size_id');
}
}
