<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class TranslationService
{
    public static function translate(string $text): string
    {
        try {

            // محاولة الاتصال بخدمة الترجمة
            $response = Http::timeout(10)
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => $text,
                    'langpair' => 'ar|en',
                ]);

            // إذا فشل الطلب أو حدث خطأ في HTTP
            if ($response->failed()) {
                return $text;
            }

            // قراءة البيانات
            $data = $response->json();

            // التحقق من وجود الترجمة
            if (
                !isset($data['responseData']['translatedText']) ||
                empty($data['responseData']['translatedText'])
            ) {
                return $text;
            }

            // إرجاع الترجمة
            return $data['responseData']['translatedText'];

        } catch (Exception $e) {

            // في حالة عدم وجود إنترنت أو Timeout
            // أو حدوث أي خطأ أثناء الاتصال بالخدمة
            return $text;
        }
    }
}

