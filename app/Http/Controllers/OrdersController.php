<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
     public function Orders()
{
    try {

        $orders = Order::with([
            'customer',
            'address',
            'items.product',
            'payment.method'
        ])
        ->latest()
        ->get();

        return view('Orders.orders', compact('orders'));

    } catch (\Exception $e) {
        return redirect()->back()->with('error','حدث خطأ أثناء جلب الطلبات');

    }
}
    public function OrderDetails($orderID)
{
    try {

        $order = Order::with([
            'customer',
            'address',
            'items.product',
            'payment.method'
        ])->findOrFail($orderID);

        return view('Orders.OrderDetails', compact('order'));

    } catch (\Exception $e) {

        return redirect()->back()->with('error','لم يتم العثور على الطلب');

    }
}
public function updateStatus(Request $request, $id)
{
    try {

        $order = Order::findOrFail($id);

        $order->status = $request->status;

        $order->save();

        return response()->json([
            'success' => true,
            'status' => $order->status
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false
        ]);

    }
}
}
