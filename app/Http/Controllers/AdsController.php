<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class adsController extends Controller
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
     * عرض نموذج إضافة إعلان عادي
     */
    public function AddAds()
    {
        $isLinkAd = false;
        return view('Ads.addads', compact('isLinkAd'));
    }

    /**
     * عرض نموذج إضافة إعلان برابط
     */
    public function AddAdsLink()
    {
        $isLinkAd = true;
        return view('Ads.addads', compact('isLinkAd'));
    }

    /**
     * حفظ الإعلان الجديد (سواء عادي أو برابط)
     */
    public function store(Request $request)
{
    $request->validate([
        'AdsName'       => 'required|string|max:255',
        'AdsImage'      => 'required|image|mimes:jpg,jpeg,png,webp,mp4|max:20480',
        'offerDuration' => 'required|numeric',
        'type'          => 'required|string|in:image,general,link', // التحقق من نوع الإعلان
        'AdsLink'       => 'nullable|url',
        'is_active'     => 'required|boolean',
    ]);

    try {
        $imageName = null;

        if ($request->hasFile('AdsImage')) {
            $imageName = time() . '_' . $request->file('AdsImage')->getClientOriginalName();
            $request->file('AdsImage')->move(public_path('storage/uploads/Advertisement'), $imageName);
        }

        $offerDuration = (int) $request->offerDuration;
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($offerDuration);

        Advertisement::create([
            'AdsName'      => $request->AdsName,
            'AdsImage'     => $imageName,
            'type'         => $request->type, // تخزين النوع المختار (image أو general)
            'AdsLink'      => $request->AdsLink,
            'is_active'    => $request->is_active,
            'expires_at'   => $endDate,
        ]);

        return redirect('/ads')->with('success', 'تم إضافة الإعلان بنجاح');

    } catch (\Throwable $th) {
        // اطبع الخطأ الفعلي بدلاً من الرسالة العامة لمعرفة المشكلة
        dd($th->getMessage());
        return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الإعلان');
    }
}

    /**
     * تبديل حالة الإعلان (نشط / غير نشط)
     */
    public function update($id)
    {
        try {
            $ads = Advertisement::findOrFail($id);
            $ads->is_active = !$ads->is_active;
            $ads->update();

            $msg = $ads->is_active ? 'تم تفعيل حالة الإعلان' : 'تم إلغاء تفعيل حالة الإعلان';
            return redirect()->back()->with('success', $msg);

        } catch (\Throwable $th) {
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