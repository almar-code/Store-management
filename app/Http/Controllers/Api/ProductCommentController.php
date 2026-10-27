<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use App\Services\TranslationService;

class ProductCommentController extends Controller
{
    // جلب التعليقات مع بيانات العميل (الاسم والصورة)
    public function getProductComments($p_id)
    {
        $comments = ProductComment::where('p_id', $p_id)
            ->with('customer:customer_id,name,profile_image')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $comments
        ], 200);
    }

    // إضافة تعليق وإعادة القائمة المحدثة مباشرة
   public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'p_id'        => 'required|integer',
            'comment_ar'  => 'required|string',
            'comment_en'  => 'required|string',
            'stars'       => 'required|numeric',
            'sentiment'   => 'nullable|string', 
        ]);
         // الترجمة
            $commenten = TranslationService::translate(
                $request->comment_en
            );

        ProductComment::create([
            'customer_id' => $request->customer_id,
            'p_id'        => $request->p_id,
            'comment_ar'  => $request->comment_ar,
            'comment_en'  => $request->commenten,
            'stars'       => $request->stars,
            'sentiment'   => $request->sentiment, 
        ]);

        $comments = ProductComment::where('p_id', $request->p_id)
            ->with('customer:customer_id,name,profile_image')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'تم إضافة التعليق بنجاح',
            'data'    => $comments
        ], 201);
    }

    // حذف التعليق
    public function destroy(Request $request, $comment_id)
    {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        $comment = ProductComment::where('comment_id', $comment_id)
            ->where('customer_id', $request->customer_id)
            ->first();

        if (!$comment) {
            return response()->json([
                'status' => false,
                'message' => 'التعليق غير موجود أو ليس لديك صلاحية لحذفه'
            ], 404);
        }

        $productId = $comment->p_id;
        $comment->delete();

        $comments = ProductComment::where('p_id', $productId)
            ->with('customer:customer_id,name,profile_image')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف التعليق بنجاح',
            'data' => $comments
        ], 200);
    }
}