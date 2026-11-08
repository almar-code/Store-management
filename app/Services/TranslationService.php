<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class TranslationService
{
    /**
     * ترجمة نص بين العربية والإنجليزية
     *
     * @param string      $text النص المراد ترجمته
     * @param string      $to   اللغة المراد الترجمة إليها (الافتراضي: en)
     * @param string|null $from لغة النص الأصلي (إذا لم تُحدَّد تُعتبر اللغة الأخرى تلقائياً)
     *
     * أمثلة:
     *  TranslationService::translate($text);        // من العربية إلى الإنجليزية (الافتراضي)
     *  TranslationService::translate($text, 'ar');  // من الإنجليزية إلى العربية
     */
    public static function translate(string $text, string $to = 'en', ?string $from = null): string
    {
        // إذا لم تُحدَّد لغة المصدر: نعتبرها عكس لغة الهدف
        $from = $from ?? ($to === 'ar' ? 'en' : 'ar');

        // لا داعي للاتصال بالخدمة إذا كان النص فارغاً أو اللغتان متطابقتين
        if (trim($text) === '' || $from === $to) {
            return $text;
        }

        try {

            // محاولة الاتصال بخدمة الترجمة
            $response = Http::timeout(10)
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => $text,
                    'langpair' => $from . '|' . $to,
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