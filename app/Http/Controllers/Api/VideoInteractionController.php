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

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video_file' => 'required|mimes:mp4,mov,ogg,qt|max:51200',
        ]);

        try {
            // 1. تخزين الفيديو في المجلد فقط (بدون قاعدة البيانات)
            $videoFile = $request->file('video_file');
            $videoName = time() . '_' . $videoFile->getClientOriginalName();

            $destinationPath = public_path('storage/uploads/videos');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $videoFile->move($destinationPath, $videoName);

            // 2. إرسال الفيديو إلى إنستجرام عبر n8n
            $instagramSent = false;
            try {
                $videoUrl = rtrim(config('app.url'), '/') . '/storage/uploads/videos/' . $videoName;

                $caption  = "🎬 وصلنا حديثاً ✨\n\n";
                $caption .= "تصفحوا متجرنا الآن للمزيد من التفاصيل والطلب! 🛍️";

                $response = \Illuminate\Support\Facades\Http::timeout(5)->post(
                    'https://almar33.app.n8n.cloud/webhook/Instagram-reel',
                    [
                        'video_url' => $videoUrl,
                        'caption'   => $caption,
                    ]
                );

                $instagramSent = $response->successful();
            } catch (\Throwable $webhookException) {
                \Illuminate\Support\Facades\Log::error('n8n Instagram Webhook Error: ' . $webhookException->getMessage());
            }

            // 3. الرد إلى n8n
            return response()->json([
                'success'        => true,
                'message'        => 'تم رفع الفيديو بنجاح',
                'video_name'     => $videoName,
                'instagram_sent' => $instagramSent,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'فشل رفع الفيديو: ' . $th->getMessage(),
            ], 500);
        }
    }

}