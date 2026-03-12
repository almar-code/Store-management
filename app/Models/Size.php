<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class Size extends Model
{
    protected $table = 'sizes';

    protected $primaryKey = 'size_id';
    protected $fillable = [
    'size_name',
    'p_id'
];

   public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }


}
