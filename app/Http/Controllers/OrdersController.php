<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStatusNotifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    // قائمة الطلبات
    public function Orders()
    {
        $orders = Order::with(['customer', 'address', 'payment.method'])
            ->withSum('orderItems as items_qty', 'quantity')
            ->latest()
            ->get();

        // أرقام البطاقات العلوية
        $stats = [
            'total'     => $orders->count(),
            'active'    => $orders->whereIn('status', ['pending', 'processing', 'ready', 'shipped'])->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'revenue'   => $orders->where('status', 'completed')->sum('total_price'),
        ];

        // عدد الطلبات لكل حالة
        $statusCounts = $orders->countBy('status');

        return view('Orders.orders', compact('orders', 'stats', 'statusCounts'));
    }

    // تفاصيل الطلب
    public function OrderDetails($orderID)
    {
        $order = Order::with([
            'customer',
            'address',
            'orderItems.product',
            'orderItems.color',
            'orderItems.size',
            'payment.method',
        ])->find($orderID);

        if (!$order) {
            return redirect('/orders')->with('error', 'لم يتم العثور على الطلب');
        }

        return view('Orders.orderDetails', compact('order'));
    }

    // تغيير حالة الطلب واشعار العميل
    public function updateStatus(Request $request, $id, OrderStatusNotifier $notifier)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $order = Order::with('customer')->findOrFail($id);

        // نفس الحالة: لا تحديث ولا إشعار
        if ($order->status === $request->status) {
            return response()->json(['success' => true, 'status' => $order->status]);
        }

        $order->update(['status' => $request->status]);

        $notifier->notify($order);

        return response()->json([
            'success' => true,
            'status'  => $order->status,
            'label'   => $order->statusLabel(),
        ]);
    }
}