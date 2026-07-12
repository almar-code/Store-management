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
public function colors()
    {
        return $this->hasMany(Color::class, 'p_id', 'p_id');
    }
    // app/Models/Product.php

public function favoritedBy()
{
    return $this->belongsToMany(Customer::class, 'favorites', 'p_id', 'customer_id');
}
    // علاقة المنتج مع الألوان

// علاقة المنتج مع المقاسات (افترضت أن اسم الجدول والـ Model هو Size والربط عبر p_id)
public function sizes() {
    return $this->hasMany(Size::class, 'p_id', 'p_id');
}
public function latestVideo()
{
    // سيعود بأحدث فيديو مضاف، وإذا لم يوجد سيرجع null تلقائياً
    return $this->hasOne(Video::class, 'product_id', 'p_id')->latest('video_id');
}
}
