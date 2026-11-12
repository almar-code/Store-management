<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'order_id',
        'method_id',
        'amount',
        'transaction_id',
        'provider',
        'provider_payment_intent_id',
        'status',
        'store_payment_account_id',
        'store_account_name',
        'store_account_number',
        'customer_bank_name',
        'customer_transaction_reference',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

        // طرق الدفع: الاسم والأيقونة
    public const PROVIDERS = [
        'cash'          => ['label' => 'الدفع عند الاستلام', 'icon' => 'bi-cash-coin'],
        'wallet'        => ['label' => 'محفظة إلكترونية', 'icon' => 'bi-wallet2'],
        'bank_account'  => ['label' => 'حساب بنكي', 'icon' => 'bi-bank2'],
        'bank_transfer' => ['label' => 'حوالة بنكية', 'icon' => 'bi-bank'],
        'stripe'        => ['label' => 'بطاقة بنكية', 'icon' => 'bi-credit-card'],
    ];
        // حساب المتجر الذي اختاره العميل
    public function storeAccount()
    {
        return $this->belongsTo(StorePaymentAccount::class, 'store_payment_account_id', 'store_payment_account_id');
    }

    // حالات المراجعة
    public const REVIEW_STATES = [
        'pending'   => 'بانتظار المراجعة',
        'confirmed' => 'تم الاستلام',
        'rejected'  => 'مرفوض',
    ];

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id', 'method_id');
    }
    // تفصل البنك عن المحفظة حسب نوع حساب المتجر
    public function methodKey(): string
    {
        if ($this->provider !== 'bank_wallet') {
            return $this->provider ?? 'cash';
        }

        return $this->storeAccount?->account_type === 'bank' ? 'bank_account' : 'wallet';
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // المدير الذي راجع الدفع
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    // حالة موحدة لكل طرق الدفع
    public function reviewState(): string
    {
        if ($this->verification_status && $this->verification_status !== 'pending') {
            return $this->verification_status;
        }

        // البطاقة تتأكد تلقائيا من Stripe
        if ($this->provider === 'stripe') {
            return match ($this->status) {
                'completed', 'succeeded' => 'confirmed',
                'failed' => 'rejected',
                default => 'pending',
            };
        }

        return 'pending';
    }

       public function providerInfo(): array
    {
        return self::PROVIDERS[$this->methodKey()] ?? [
            'label' => $this->method->method_name ?? ($this->provider ?? '-'),
            'icon'  => 'bi-cash',
        ];
    }
}