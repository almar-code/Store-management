<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $table = 'advertisement';

    protected $primaryKey = 'ads_id';

    protected $fillable = [
        'AdsName',
        'AdsImage',
        'AdsLink',
        'is_active',
        'expires_at',
        'type',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'date',
        ];
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_id',
            'p_id'
        );
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'advertisement_product',
            'ads_id',
            'p_id'
        )->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query
            ->where('is_active', true)
            ->whereDate('expires_at', '>=', now()->toDateString());
    }
}
