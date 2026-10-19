<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePaymentAccount extends Model
{
    use HasFactory;

    protected $table = 'store_payment_accounts';

    protected $primaryKey = 'store_payment_account_id';

    protected $fillable = [
        'store_payment_account_id',
        'account_type',
        'provider_name',
        'provider_name_en',
        'account_holder_name',
        'account_number',
        'iban',
        'instructions',
        'is_active',
        'sort_order',
    ];
}