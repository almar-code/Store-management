<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ArPHP\I18N\Arabic;
use Throwable;

class OrderController extends Controller
{
    public function getUserOrders(Request $request, $customerId)
    {
        $orders = Order::with('address')
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function cancelOrder(Request $request, $orderId)
    {
        $order = DB::table('orders')
            ->where('order_id', $orderId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب غير موجود',
            ], 404);
        }

        DB::table('orders')
            ->where('order_id', $orderId)
            ->update([
                'status' => 'cancelled',
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الطلب بنجاح',
        ]);
    }

   public function generateInvoice($id, Request $request)
{
    try {
        $order = Order::with(['address', 'orderItems.product'])
            ->where('order_id', $id)
            ->firstOrFail();

        $arabic = new Arabic();

        $arabicText = function ($text) use ($arabic) {
            if ($text === null || $text === '') {
                return '';
            }
            return $arabic->utf8Glyphs((string) $text);
        };

        $storeName = 'Nice Abayas';
        $lang = $request->header('Accept-Language', 'en'); // الافتراضي عربي

        $viewName = ($lang === 'en') ? 'invoices.order_invoice_en' : 'invoices.order_invoice';

        $pdf = Pdf::loadView(
            $viewName,
            compact('order', 'storeName', 'arabicText')
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('invoice_order_' . $order->order_id . '.pdf');

    } catch (Throwable $e) {
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'تعذر إنشاء الفاتورة',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
