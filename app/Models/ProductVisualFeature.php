<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVisualFeature extends Model
{
    protected $table = 'product_visual_features';

    protected $primaryKey = 'feature_id';

    protected $fillable = [
        'color_id',
        'image_hash',
        'dominant_color',
        'image_labels',
        'image_features_json',
    ];

    protected $casts = [
        'image_features_json' => 'array',
    ];
}