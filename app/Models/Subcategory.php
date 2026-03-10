<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    protected $primaryKey = 'subcat_id';

    protected $fillable = [
        'subcat_name',
        'subcat_name_en',
        'subcat_image',
        'cat_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'cat_id');
    }

}
