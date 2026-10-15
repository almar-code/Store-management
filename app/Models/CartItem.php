<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $primaryKey = 'cart_item_id';

    protected $fillable = [
        'cart_id',
        'product_id',
        'color_id',
        'size_id',
        'quantity',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(
            Cart::class,
            'cart_id',
            'cart_id'
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class,
            'product_id',
            'p_id'
        );
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(
            Color::class,
            'color_id',
            'color_id'
        );
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(
            Size::class,
            'size_id',
            'size_id'
        );
    }
}