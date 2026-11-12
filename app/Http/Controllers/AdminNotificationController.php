<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * إشعارات المستخدم المسموحة حسب صلاحياته الحالية.
     */
    private function userNotifications()
    {
        $types = AdminNotificationService::allowedTypes(
            AdminNotificationService::currentUserRoles()
        );

        return AdminNotification::where('user_id', Auth::id())
            ->whereIn('type', $types);
    }

    /**
     * آخر الإشعارات وعدد غير المقروء (يستدعيها الجرس كل 30 ثانية).
     */
    public function feed()
    {
        $items = $this->userNotifications()
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->notification_id,
                'type' => $n->type,
                'title' => $n->title,
                'body' => $n->body,
                'open_url' => url('/notifications/' . $n->notification_id . '/open'),
                'time' => $n->created_at?->locale('ar')->diffForHumans(),
                'read' => $n->read_at !== null,
            ]);

        return response()->json([
            'unread' => $this->userNotifications()->unread()->count(),
            'items' => $items,
        ]);
    }

    /**
     * تعليم الإشعار كمقروء ثم فتح صفحته.
     */
    public function open($id)
    {
        $notification = $this->userNotifications()->findOrFail($id);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect($notification->url ?: '/');
    }

    public function readAll()
    {
        $this->userNotifications()
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}