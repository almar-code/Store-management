<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    // بريد التواصل الظاهر في الصفحات
    private string $contactEmail = 'alftyhyly35@gmail.com';

    // تحديد اللغة من الرابط
    private function lang(Request $request): string
    {
        return $request->query('lang') === 'en' ? 'en' : 'ar';
    }

    // صفحة سياسة الخصوصية
    public function privacy(Request $request)
    {
        return view('Legal.privacy', [
            'lang' => $this->lang($request),
            'contactEmail' => $this->contactEmail,
        ]);
    }

    // صفحة حذف البيانات
    public function dataDeletion(Request $request)
    {
        return view('Legal.data-deletion', [
            'lang' => $this->lang($request),
            'contactEmail' => $this->contactEmail,
        ]);
    }
}
