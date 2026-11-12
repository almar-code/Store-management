<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use Illuminate\Http\Request;

class CustomerNotificationController extends Controller
{
    // جلب إشعارات العميل
    // GET /api/notifications?customer_id=5
    public function index(Request $request)
    {
        $request->validate(['customer_id' => 'required|integer']);

        $items = CustomerNotification::where('customer_id', $request->customer_id)
            ->latest()
            ->take(50)
            ->get();

        return response()->json([
            'status' => true,
            'unread_count' => $items->whereNull('read_at')->count(),
            'data' => $items,
        ]);
    }

    // تعليم إشعار كمقروء
    // POST /api/notifications/{id}/read
    public function markRead(Request $request, int $id)
    {
        $this->query($request)
            ->where('notification_id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['status' => true]);
    }

    // تعليم الكل كمقروء
    // POST /api/notifications/read-all
    public function markAllRead(Request $request)
    {
        $this->query($request)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => true]);
    }

    // حذف إشعار
    // DELETE /api/notifications/{id}
    public function destroy(Request $request, int $id)
    {
        $this->query($request)
            ->where('notification_id', $id)
            ->delete();

        return response()->json(['status' => true]);
    }

    // كل عملية تخص إشعارات نفس العميل فقط
    private function query(Request $request)
    {
        $request->validate(['customer_id' => 'required|integer']);

        return CustomerNotification::where('customer_id', $request->customer_id);
    }
}
