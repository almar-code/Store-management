<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $primaryKey = 'img_id';
    protected $fillable = [
    'img_url',
    'color_id'
];
}
