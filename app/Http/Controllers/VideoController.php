<?php

namespace App\Http\Controllers;
use App\Models\Video;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\VideoStat;
class VideoController extends Controller
{
        // جلب الريلز على دفعات
    // GET /api/get-all-videos?page=1&per_page=6&seed=123
    public function getVideosApi(Request $request)
    {
        try {
            $perPage = min((int) $request->input('per_page', 6), 20);
            $seed = (int) $request->input('seed', 1);

            // نفس الترتيب العشوائي في كل الدفعات
            $videos = Video::with(['product', 'stats'])
                ->withCount('comments')
                ->inRandomOrder($seed)
                ->paginate($perPage);

            return response()->json([
                'status' => true,
                'data' => $videos->getCollection()->map(fn ($v) => $this->formatVideo($v)),
                'meta' => [
                    'current_page' => $videos->currentPage(),
                    'last_page'    => $videos->lastPage(),
                    'total'        => $videos->total(),
                    'total_views'  => (int) VideoStat::sum('views_count'),
                    'total_likes'  => (int) VideoStat::sum('likes_count'),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب الفيديوهات',
            ], 500);
        }
    }

    // جلب ريل واحد
    public function getVideoApi($id)
    {
        $video = Video::with(['product', 'stats'])->withCount('comments')->find($id);

        if (!$video) {
            return response()->json(['status' => false], 404);
        }

        return response()->json(['status' => true, 'data' => $this->formatVideo($video)]);
    }

    // شكل بيانات الريل
    private function formatVideo(Video $video): array
    {
        $product = $video->product;
        $stats = $video->stats;

        return [
            'video_id'        => $video->video_id,
            'video_path'      => $video->video_path,
            'product_image'   => $product?->p_image,
            'product_id'      => $video->product_id,
            'product_name'    => $product?->p_name,
            'product_name_en' => $product?->p_name_en,
            'product_price'   => $product?->p_price,
            'likes_count'     => $stats->likes_count ?? 0,
            'saves_count'     => $stats->saves_count ?? 0,
            'shares_count'    => $stats->shares_count ?? 0,
            'views_count'     => $stats->views_count ?? 0,
            'comments_count'  => $video->comments_count,
        ];
    }    public function AddVideoToProduct($productID)
    {
        try {
            $productName = Product::findOrFail($productID)->p_name;
            return view('Videos.add_video', compact('productName', 'productID'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المنتج  ');
        }
    }
    public function AddVideo()
    {
        try {

            return view('Videos.add_video');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء فتح الصفحة');

        }
    }
    public function store(Request $request, $productID = null)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'video_file' => 'required|mimes:mp4,mov,ogg,qt|max:51200', // حد أقصى 50 ميجا مثلاً
        ]);

        try {
            // 2. معالجة ملف الفيديو
            if ($request->hasFile('video_file')) {
                $videoFile = $request->file('video_file');

                // إنشاء اسم فريد للملف لمنع التكرار
                $videoName = time() . '_' . $videoFile->getClientOriginalName();

                // نقل الملف إلى المجلد المطلوب في public
                $videoFile->move(public_path('storage/uploads/videos'), $videoName);

                // 3. تخزين البيانات في قاعدة البيانات
                $video = Video::create([
                    'video_path' => $videoName,
                    'product_id' => $productID, // سيأخذ القيمة إذا وُجدت أو يظل null
                ]);

                // --- بداية كود الأتمتة وإرسال البيانات إلى n8n ---
                try {
                    // بناء رابط الفيديو المباشر كما هو في الـ API
                    $videoUrl = rtrim(config('app.url'), '/') . '/storage/uploads/videos/' . $videoName;

                    // جلب اسم المنتج إذا كان الفيديو مرتبطاً بمنتج، وإلا نعتبره فيديو عام
                    $productName = 'فيديو عام';
                    if ($productID) {
                        $product = Product::find($productID);
                        if ($product) {
                            $productName = $product->p_name;
                        }
                    }

                    // تجهيز الكابشن (الوصف) للريل
                    $caption = "🎬 وصلنا حديثاً: " . $productName . " ✨\n\n";
                    $caption .= "تصفحوا متجرنا الآن للمزيد من التفاصيل والطلب! 🛍️";

                    // إرسال الطلب إلى n8n (باستخدام رابط الـ Test حالياً)
                    \Illuminate\Support\Facades\Http::timeout(5)->post('https://almar3.app.n8n.cloud/webhook-test/Instagram', [
                        'video_url' => $videoUrl,
                        'caption'   => $caption
                    ]);

                } catch (\Throwable $webhookException) {
                    // نضع هذا داخل catch مستقلة حتى لو فشل الاتصال بـ n8n لأي سبب، لا يتعطل حفظ الفيديو في متجرك
                    \Illuminate\Support\Log::error('n8n Webhook Error: ' . $webhookException->getMessage());
                }
                // --- نهاية كود الأتمتة ---

                return redirect()->back()->with('success', 'تم إضافة الفيديو بنجاح وجاري إرساله للانستجرام');
            }

            return redirect()->back()->with('error', 'الرجاء اختيار ملف فيديو');

        } catch (\Throwable $th) {
            // طباعة الخطأ الفعلي للتصحيح (اختياري)
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الفيديو: ' . $th->getMessage());
        }
    }
    public function index()
    {
        // جلب الفيديوهات مع بيانات المنتج المرتبط بها لتحسين الأداء
        $videos = Video::with('product')->orderBy('created_at', 'desc')->get();
        return view('Videos.videos', compact('videos'));
    }
    public function destroy($id)
    {
        try {
            $video = Video::findOrFail($id);

            // مسار الملف في المجلد
            $filePath = public_path('storage/uploads/videos/' . $video->video_path);

            // حذف الملف الفيزيائي من السيرفر إذا وجد
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            // حذف السجل من قاعدة البيانات
            $video->delete();

            return redirect()->back()->with('success', 'تم حذف الفيديو بنجاح');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة الحذف');
        }
    }
}
