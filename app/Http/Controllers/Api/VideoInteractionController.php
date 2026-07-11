<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoStat;
use Illuminate\Http\Request;

class VideoInteractionController extends Controller
{
    // 1. الضغط على زر الإعجاب (زيادة أو نقصان)
    public function toggleLike(Request $request, $id)
    {
        try {
            
            // 1. البحث عن السجل الخاص بالفيديو في جدول الإحصائيات، وإذا لم يكن موجوداً سيقوم بإنشائه تلقائياً
            $stat = VideoStat::firstOrCreate(
                ['video_id' => $id],
                [
                    'likes_count' => 0,
                    'saves_count' => 0,
                    'shares_count' => 0
                ]
            );

            // 2. استقبال نوع العملية من تطبيق فلاتر (هل هي زيادة أم نقصان)
            // سنفترض أن فلاتر يرسل في الـ Body متغير اسمه 'action' قيمته إما 'like' أو 'unlike'
            $action = $request->input('action', 'like'); 

            if ($action === 'like') {
                // زيادة العداد بمقدار 1 في قاعدة البيانات
                $stat->increment('likes_count');
                $message = 'تم إضافة الإعجاب بنجاح';
            } else {
                // نقصان العداد بمقدار 1 مع التأكد أنه لا ينزل تحت الصفر
                if ($stat->likes_count > 0) {
                    $stat->decrement('likes_count');
                }
                $message = 'تم إزالة الإعجاب بنجاح';
            }

            // 3. إعادة العداد الجديد للفلاتر ليتأكد من مطابقة البيانات
            return response()->json([
                'status' => true,
                'message' => $message,
                'likes_count' => $stat->likes_count
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث الإعجاب',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 2. الضغط على زر المشاركة
    public function incrementShare($id)
    {
        $stat = VideoStat::firstOrCreate(['video_id' => $id]);
        $stat->increment('shares_count');

        return response()->json([
            'status' => true,
            'shares_count' => $stat->shares_count
        ], 200);
    }

    // 3. الضغط على زر الحفظ
    public function toggleSave($id)
    {
        $stat = VideoStat::firstOrCreate(['video_id' => $id]);
        $stat->increment('saves_count');

        return response()->json([
            'status' => true,
            'saves_count' => $stat->saves_count
        ], 200);
    }
}