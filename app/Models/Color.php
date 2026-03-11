<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colors';

    protected $primaryKey = 'color_id';
    protected $fillable = [
        'color_name',
        'color_name_en',
        'color_code',
        'p_id'
    ];
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'color_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }
}
