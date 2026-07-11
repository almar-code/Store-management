<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Category;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Storage;



class CategorieController extends Controller
{
public function index(Request $request)
{
    try {
        // 1. استقبال الـ ID الخاص بالقسم الرئيسي إذا أرسله فلاتر
        $categoryId = $request->query('category_id');

        // 2. بناء الاستعلام برمجياً
        $query = Subcategory::query();

        if ($categoryId) {
            // إذا تم إرسال المعرف، نجلب الفئات التابعة لهذا القسم فقط
            $query->where('cat_id', $categoryId); 
        } else {
            // إذا لم يتم الإرسال، نتركها عشوائية كما كانت في كودك الأصلي
            $query->inRandomOrder();
        }

        $subcategories = $query->get()->map(function ($subcategory) {
            return $subcategory;
        });

        return response()->json([
            'status' => true,
            'subcategories' => $subcategories
        ], 200);

    } catch (\Exception $e){
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء جلب البيانات',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
}
