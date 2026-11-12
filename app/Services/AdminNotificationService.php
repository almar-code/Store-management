<?php

namespace App\Services;

use App\Jobs\SendAdminWhatsApp;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * إشعارات لوحة الإدارة: إشعار في الجرس + واتساب للموظفين المعنيين.
 */
class AdminNotificationService
{
    /**
     * من يستلم كل نوع من الإشعارات.
     * عند إضافة إشعار جديد نضيف نوعه وصلاحياته هنا فقط.
     */
    public const TYPE_ROLES = [
        'new_order' => ['مدير المتجر', 'مدير المبيعات'],
    ];

    /**
     * إشعار طلب جديد. لا يرمي أخطاء حتى لا يتأثر إنشاء الطلب.
     */
    public static function newOrder(int $orderId): void
    {
        try {
            $order = DB::table('orders')
                ->leftJoin('customers', 'customers.customer_id', '=', 'orders.customer_id')
                ->where('orders.order_id', $orderId)
                ->first([
                    'orders.order_id',
                    'orders.total_price',
                    'orders.payment_status',
                    'customers.name as customer_name',
                ]);

            if (!$order) {
                return;
            }

            $url = url('/orderDetails/' . $order->order_id);
            $customer = $order->customer_name ?: 'عميل';
            $total = number_format((float) $order->total_price, 2);
            $payment = $order->payment_status === 'paid' ? 'مدفوع' : 'بانتظار التحقق';

            $title = 'طلب جديد #' . $order->order_id;
            $body = "العميل: {$customer} | الإجمالي: {$total} | الدفع: {$payment}";

            $whatsApp = "طلب جديد في متجر Nice\n"
                . "رقم الطلب: #{$order->order_id}\n"
                . "العميل: {$customer}\n"
                . "الإجمالي: {$total}\n"
                . "حالة الدفع: {$payment}\n"
                . "تفاصيل الطلب:\n{$url}";

            self::send('new_order', $title, $body, $url, $order->order_id, $whatsApp);
        } catch (Throwable $e) {
            Log::error('New order notification failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * دالة عامة نستخدمها لبقية الإشعارات لاحقاً.
     */
    public static function send(
        string $type,
        string $title,
        ?string $body,
        ?string $url,
        ?int $relatedId = null,
        ?string $whatsAppMessage = null
    ): void {
        $roles = self::TYPE_ROLES[$type] ?? [];

        if (empty($roles)) {
            return;
        }

        $users = self::usersWithRoles($roles);

        foreach ($users as $user) {
            AdminNotification::create([
                'user_id' => $user->user_id,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'url' => $url,
                'related_id' => $relatedId,
            ]);

            if ($whatsAppMessage && !empty($user->phone)) {
                SendAdminWhatsApp::dispatch($user->phone, $whatsAppMessage);
            }
        }
    }

    /**
     * أنواع الإشعارات المسموحة لمستخدم حسب صلاحياته.
     */
    public static function allowedTypes(array $userRoles): array
    {
        return array_keys(array_filter(
            self::TYPE_ROLES,
            fn ($roles) => !empty(array_intersect($roles, $userRoles))
        ));
    }

    /**
     * صلاحيات المستخدم الحالي الفعالة.
     */
    public static function currentUserRoles(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        return $user->userPermissions()
            ->where('is_active', 1)
            ->with('permission')
            ->get()
            ->pluck('permission.permission_name')
            ->map(fn ($name) => trim((string) $name))
            ->all();
    }

    /**
     * المستخدمون الذين لديهم صلاحية فعالة من القائمة.
     */
    public static function usersWithRoles(array $roles): Collection
    {
        return User::query()
            ->whereHas('userPermissions', function ($q) use ($roles) {
                $q->where('is_active', 1)
                    ->whereHas('permission', function ($p) use ($roles) {
                        $p->whereIn(DB::raw('TRIM(permission_name)'), $roles);
                    });
            })
            ->get(['user_id', 'phone']);
    }
}