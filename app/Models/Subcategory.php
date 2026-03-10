<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
     protected $table = 'subcategories';

    protected $primaryKey = 'subcat_id';

    protected $fillable = [
        'subcat_name',
        'subcat_name_en'
    ];
}
