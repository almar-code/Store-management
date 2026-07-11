<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // 1. جلب تعليقات فيديو معين مع بيانات العميل الذي علق
   public function getComments($id)
{
    $comments = VideoComment::where(
            'video_id',
            $id
        )
        ->with([
            'customer:customer_id,name,profile_image'
        ])
        ->latest()
        ->get();

    $data = $comments->map(function ($comment) {

        return [
            'id' => $comment->id,
            'commentText' =>$comment->comment_text,
            'customerName' =>$comment->customer->name ?? '',
            'customerImage' =>$comment->customer->profile_image,
            'videoId' =>$comment->video_id,
            'createdAt' =>$comment->created_at->toDateTimeString(),
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $data
    ], 200);
}

    // 2. حفظ تعليق جديد في قاعدة البيانات
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment_text' => 'required|string|max:500',
            'customer_id' => 'required|integer' // سنرسله مؤقتاً من فلاتر حتى يكتمل نظام الحماية لديك
        ]);

       $comment = VideoComment::create([
        'video_id' => $id,
        'customer_id' => $request->customer_id,
        'comment_text' => $request->comment_text,
    ]);

    $comment->load('customer'); 

   return response()->json([

    'id' => $comment->id,
    'commentText' =>$comment->comment_text,
    'customerName' =>$comment->customer->name,
    'customerImage' =>$comment->customer->profile_image,
    'videoId' =>$comment->video_id,
    'createdAt' =>$comment->created_at->toDateTimeString(),
], 201);
    }
}