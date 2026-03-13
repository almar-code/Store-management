<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';

    protected $primaryKey = 'discount_id';

    protected $fillable = [
        'discount_perce',
        'p_id',
        'end_date'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }
}
