<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Product;
use App\Services\AdNotifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdsController extends Controller
{
    /**
     * عرض صفحة قائمة الإعلانات
     */
    public function Ads()
    {
        try {
            $ads = Advertisement::with('product')->get();
            return view('Ads.ads', compact('ads'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الإعلانات');
        }
    }

    /**
     * عرض نموذج إضافة إعلان خاص
     */
    public function AddAds()
    {
        $adType = 'image';
        return view('Ads.addads', compact('adType'));
    }

    /**
     * عرض نموذج إضافة إعلان عام مع منتجاته
     */
    public function AddAdsGeneral()
    {
        $adType = 'general';
        $products = Product::select('p_id', 'p_name', 'p_image')->orderBy('p_name')->get();
        return view('Ads.addads', compact('adType', 'products'));
    }

    /**
     * عرض نموذج إضافة إعلان برابط
     */
    public function AddAdsLink()
    {
        $adType = 'link';
        return view('Ads.addads', compact('adType'));
    }

    /**
     * حفظ الإعلان الجديد (خاص أو عام أو برابط)
     */
    public function store(Request $request, AdNotifier $notifier)
    {
        $request->validate([
            'AdsName'       => 'required|string|max:255',
            'AdsImage'      => 'required|image|mimes:jpg,jpeg,png,webp,mp4|max:20480',
            'offerDuration' => 'required|numeric|min:1',
            'type'          => 'required|string|in:image,general,link',
            'AdsLink'       => 'nullable|url',
            'is_active'     => 'required|boolean',
            // منتجات الإعلان العام
            'product_ids'   => 'required_if:type,general|array|min:1',
            'product_ids.*' => 'integer|exists:products,p_id',
        ], [
            'product_ids.required_if' => 'يرجى اختيار منتج واحد على الأقل للإعلان العام',
            'product_ids.min'         => 'يرجى اختيار منتج واحد على الأقل للإعلان العام',
            'product_ids.*.exists'    => 'أحد المنتجات المختارة غير موجود',
        ]);

        try {
            $imageName = time() . '_' . $request->file('AdsImage')->getClientOriginalName();
            $request->file('AdsImage')->move(public_path('storage/uploads/Advertisement'), $imageName);

            $endDate = Carbon::today()->addDays((int) $request->offerDuration);

            DB::transaction(function () use ($request, $imageName, $endDate, $notifier) {
                $ad = Advertisement::create([
                    'AdsName'    => $request->AdsName,
                    'AdsImage'   => $imageName,
                    'type'       => $request->type,
                    'AdsLink'    => $request->type === 'link' ? $request->AdsLink : null,
                    'is_active'  => $request->is_active,
                    'expires_at' => $endDate,
                ]);

                // ربط المنتجات وإرسال الإشعار للإعلان العام فقط
                if ($ad->type === 'general') {
                    $ad->products()->attach(array_unique($request->product_ids));
                    $notifier->notifyIfNeeded($ad);
                }
            });

            return redirect('/ads')->with('success', 'تم إضافة الإعلان بنجاح');

        } catch (\Throwable $th) {
            report($th);
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الإعلان')->withInput();
        }
    }

    /**
     * تبديل حالة الإعلان (نشط / غير نشط)
     */
    public function update(Request $request, AdNotifier $notifier, $id)
    {
        try {
            $ads = Advertisement::findOrFail($id);

            // طلب التجديد لا يعطل إعلانا مفعلا (ضغط مزدوج أو صفحة قديمة)
            if ($ads->is_active && $request->isMethod('post')) {
                return redirect()->back()->with('success', 'الإعلان مفعل بالفعل');
            }

            // تعطيل مباشر
            if ($ads->is_active) {
                $ads->update(['is_active' => false]);
                return redirect()->back()->with('success', 'تم إلغاء تفعيل حالة الإعلان');
            }

            // الإعلان المنتهي يحتاج مدة جديدة تبدأ من اليوم
            if ($ads->expires_at && $ads->expires_at->lt(Carbon::today())) {
                $duration = (int) $request->input('offerDuration');

                if ($duration < 1) {
                    return redirect()->back()->with('error', 'يرجى تحديد مدة جديدة لتفعيل الإعلان المنتهي');
                }

                $ads->expires_at = Carbon::today()->addDays($duration);
            }

            $ads->is_active = true;
            $ads->save();

            // أول تفعيل للإعلان العام يرسل الإشعار
            $notifier->notifyIfNeeded($ads);

            return redirect()->back()->with('success', 'تم تفعيل حالة الإعلان');

        } catch (\Throwable $th) {
            report($th);
            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل');
        }
    }

    /**
     * صفحة إضافة إعلان خاص بمنتج معين (من صفحة المنتجات)
     */
public function createProductAd($productId)
{
    try {
        $product = Product::findOrFail($productId);
        // استدعاء ملف addads.blade.php الموجود في مجلد Ads
        return view('Ads.addads', compact('product'));
    } catch (\Throwable $th) {
        dd($th->getMessage());
    }
}

    /**
     * حفظ إعلان خاص بمنتج
     */
   /**
     * حفظ إعلان خاص بمنتج
     */
    public function storeProductAd(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'AdsName'       => 'required|string|max:255',
            'AdsImage'      => 'nullable|image|mimes:jpg,jpeg,png,webp,mp4|max:20480',
            'offerDuration' => 'required|numeric|min:1',
            'is_active'     => 'required|boolean',
        ]);

        try {
            // معالجة رفع صورة الإعلان الجديدة إن وجدت، وإلا يتم استخدام صورة المنتج الأساسية
            $imageName = $product->p_image ?? 'default.jpg';

            if ($request->hasFile('AdsImage')) {
                $imageName = time() . '_' . $request->file('AdsImage')->getClientOriginalName();
                $request->file('AdsImage')->move(public_path('storage/uploads/Advertisement'), $imageName);
            }

            $offerDuration = (int) $request->offerDuration;
            $startDate = \Carbon\Carbon::today();
            $endDate = $startDate->copy()->addDays($offerDuration);

            Advertisement::create([
                'AdsName'      => $request->AdsName,
                'AdsImage'     => $imageName,
                'type'         => 'product', // أو 'product' حسب النظام لديك، الأغلب 'image' للإعلانات الخاصة
                'product_id'   => $product->p_id,
                'AdsLink'      => null,    // لأن الإعلان خاص بمنتج وليس برابط خارجي
                'is_active'    => $request->is_active,
                'expires_at'   => $endDate,
            ]);

            return redirect('/ads')->with('success', 'تم إضافة إعلان المنتج بنجاح');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ الإعلان: ' . $th->getMessage())->withInput();
        }
    }
    /**
     * عرض صفحة ربط المنتج بعرض عام (الجدول الوسيط)
     */
    public function assignGeneralAdForm($productId)
{
    $product = Product::findOrFail($productId);
    
    // استخدام اسم العلاقة الصحيح المعرف في مودل المنتج
    $assignedAdIds = $product->generalAdvertisements()->pluck('advertisement.ads_id')->toArray();
    
    $generalAds = Advertisement::where('type', 'general')
                ->whereNotIn('ads_id', $assignedAdIds)
                ->get();

    return view('Ads.assign_general_ad', compact('product', 'generalAds'));
}

public function storeGeneralAdAssignment(Request $request, $productId)
{
    $request->validate([
        'ads_id' => 'required|exists:advertisement,ads_id',
    ]);

    $product = Product::findOrFail($productId);
    
    // استخدام اسم العلاقة الصحيح هنا أيضاً
    $product->generalAdvertisements()->attach($request->ads_id);

    return redirect('/products')->with('success', 'تم إدراج المنتج في العرض العام بنجاح');
}
    

    /**
     * عرض المنتجات المرتبطة بإعلان عام معين (زر العين)
     */
    public function showGeneralAdProducts($adsId)
    {
        $ad = Advertisement::where('type', 'general')->findOrFail($adsId);
        $products = $ad->products; // العلاقة عبر الجدول الوسيط

        return view('Ads.general_ad_products', compact('ad', 'products'));
    }

    /**
     * إزالة منتج من عرض عام معين
     */
    public function removeProductFromGeneralAd($adsId, $productId)
    {
        $ad = Advertisement::findOrFail($adsId);
        $ad->products()->detach($productId);

        return redirect()->back()->with('success', 'تم إزالة المنتج من العرض العام بنجاح');
    }

    /**
     * حذف الإعلان نهائياً مع صورته
     */
    public function destroy($id)
    {
        try {
            $ads = Advertisement::findOrFail($id);
            $imagePath = public_path('storage/uploads/Advertisement/' . $ads->AdsImage);
            
            if (!empty($ads->AdsImage) && File::exists($imagePath)) {
                File::delete($imagePath);
            }
            
            $ads->delete();
            return redirect()->back()->with('success', 'تم حذف الإعلان وصورته بنجاح');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');
        }
    }
}