<?php

namespace App\Http\Controllers;
use App\Models\Advertisement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class adsController extends Controller
{
   public function AddAds(){
        return view('Ads.addads', []);
    }

    public function store(Request $request)
    {
        $request->validate([
            'AdsName' => 'required|string|max:255',
            'AdsImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'offerDuration' => 'required|numeric',
            'AdsLink' => 'required',
            'is_active' => 'required'
        ]);
        try {

            $imageName = time().'_'.$request->file('AdsImage')->getClientOriginalName();
            $path = $request->file('AdsImage')->storeAs('uploads/Advertisement', $imageName, 'public');

            $offerDuration = (int) $request->offerDuration;
            $startDate =Carbon::today();
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
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الاعلان ');
            
        }
    }
    
}
