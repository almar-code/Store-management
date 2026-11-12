<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Payment;
class Order extends Model
{
    protected $primaryKey = 'order_id';
    protected $fillable = [
    'customer_id',
    'address_id',
    'total_price',
    'status',
    'payment_status',
];
    // حالات الطلب: المفتاح يحفظ في القاعدة والنص للعرض
    public const STATUSES = [
        'pending'    => ['ar' => 'قيد الانتظار', 'en' => 'Pending'],
        'processing' => ['ar' => 'قيد المعالجة', 'en' => 'Processing'],
        'ready'      => ['ar' => 'جاهز', 'en' => 'Ready'],
        'shipped'    => ['ar' => 'تم الشحن', 'en' => 'Shipped'],
        'completed'  => ['ar' => 'مكتمل', 'en' => 'Completed'],
        'cancelled'  => ['ar' => 'ملغي', 'en' => 'Cancelled'],
        'refunded'   => ['ar' => 'مسترجع', 'en' => 'Refunded'],
    ];

    // اسم الحالة بالعربي او الانجليزي
    public function statusLabel(string $lang = 'ar'): string
    {
        return self::STATUSES[$this->status][$lang] ?? $this->status;
    }
        // حالات الدفع
    public const PAYMENT_STATUSES = [
        'pending'  => 'غير مدفوع',
        'paid'     => 'مدفوع',
        'failed'   => 'فشل الدفع',
        'refunded' => 'مسترد',
    ];

    public function paymentLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? ($this->payment_status ?? '-');
    }
   public function customer()
{
    return $this->belongsTo(Customer::class,'customer_id');
}

// public function address()
// {
//     return $this->belongsTo(CustomerAddress::class,'address_id');
    
// }
public function address() {
    return $this->belongsTo(CustomerAddress::class, 'address_id', 'address_id');
}

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function payment() {
        return $this->hasOne(Payment::class,'order_id');
    }

}
