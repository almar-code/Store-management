<?php

namespace App\Http\Controllers;
use App\Models\Advertisement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class adsController extends Controller
{
   public function AddAds()
        {
            return view('Ads.addads', []);
        }

    public function store(Request $request)
    {
        $request->validate([
            'AdsName' => 'required|string|max:255',
            'AdsImage' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096', // جعلتها required لضمان وجود صورة
            'offerDuration' => 'required|numeric',
            'AdsLink' => 'required',
            'is_active' => 'required'
        ]);

        try {
            $imageName = null;

            // التحقق من رفع الصورة واستخدام طريقة move المضمونة
            if ($request->hasFile('AdsImage')) {
                $imageName = time().'_'.$request->file('AdsImage')->getClientOriginalName();

                // الرفع لمجلد public مباشرة (يعمل في اللوكل والاستضافة)
                $request->file('AdsImage')->move(public_path('storage/uploads/Advertisement'), $imageName);
            }

            $offerDuration = (int) $request->offerDuration;
            $startDate = \Carbon\Carbon::today(); // إضافة \ قبل Carbon لتجنب خطأ التعريف
            $endDate = $startDate->copy()->addDays($offerDuration);

            Advertisement::create([
                'AdsName' => $request->AdsName,
                'AdsImage' => $imageName,
                'AdsLink' => $request->AdsLink,
                'is_active' => $request->is_active,
                'expires_at' => $endDate
            ]);

            return redirect()->back()->with('success', 'تم إضافة الاعلان بنجاح');

        } catch (\Throwable $th) {
            // يمكنك إرجاع $th->getMessage() إذا أردت معرفة سبب الخطأ بدقة أثناء التجربة
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الاعلان');
        }
    }
     public function Ads()
        {
            try {
                $ads = Advertisement::where('expires_at', '>=', Carbon::today())->get();

                return view('Ads.ads',compact('ads'));
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الاعلانات ');
            }
        }

    public function update($id)
        {
                try {
                    $ads = Advertisement::findOrFail($id);
                    if($ads->is_active == 1)
                        {
                            $ads->is_active = 0;
                            $ads->update();
                            return redirect()->back()->with('success', '  تم الغاء تفعيل حاله الاعلان  ');
                        }else
                        {
                            $ads->is_active = 1;
                            $ads->update();
                            return redirect()->back()->with('success', 'تم تفعيل حاله الاعلان  ');
                        }

                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل');
                }
        }

    public function destroy($id)
    {
        try {
            $ads = Advertisement::findOrFail($id);
            $imagePath = public_path('storage/uploads/Advertisement/' . $ads->AdsImage);
            if (!empty($ads->AdsImage) && \File::exists($imagePath)) {
                \File::delete($imagePath);
            }
            $ads->delete();
            return redirect()->back()->with('success', 'تم حذف الإعلان وصورته بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');
        }
    }
}
