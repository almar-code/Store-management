<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function index()
    {
        try {
            $sections = Category::inRandomOrder()->get();

            return response()->json([
                'status' => true,
                'sections' => $sections
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}