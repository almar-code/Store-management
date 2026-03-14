<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'p_id';
    protected $fillable = [
    'p_name',
    'p_name_en',
    'p_description',
    'p_description_en',
    'p_price',
    'p_image',
    'subcat_id'
];
public function subcategory()
{
    return $this->belongsTo(Subcategory::class, 'subcat_id','subcat_id');   

}
public function discount()
{
    return $this->hasOne(Discount::class,'p_id','p_id')
        ->where('end_date','>=',now());
}
}
