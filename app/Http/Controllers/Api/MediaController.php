<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    /**
     * تقديم ملفات الوسائط من public/storage مع هيدر CORS
     * حتى تُعرض الصور في Flutter Web.
     */
    public function show(string $path)
    {
        $base = realpath(public_path('storage'));
        $file = realpath(public_path('storage/' . $path));

        // حماية من الخروج خارج مجلد التخزين + التأكد أنه ملف موجود
        abort_if(
            !$base || !$file
            || !str_starts_with($file, $base . DIRECTORY_SEPARATOR)
            || !is_file($file),
            404
        );

        return response()->file($file, [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control'               => 'public, max-age=86400',
        ]);
    }
}