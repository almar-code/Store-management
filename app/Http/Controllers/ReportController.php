<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    // عرض صفحة طلب التقرير
    public function index() {
    return view('Reports.reports'); // ملف reports.blade.php
}

public function custom() {
    return view('Reports.customReport'); // ملف customReport.blade.php
}

public function ready() {
    return view('Reports.reportsReady'); // ملف reportsReady.blade.php
}

    // إرسال الطلب إلى n8n
   public function generate(Request $request)
{
    $request->validate(['report_query' => 'required|string']);

    $webhookUrl = 'https://n8n-production-7bdd.up.railway.app/webhook-test/reports';

    $response = Http::post($webhookUrl, [
        'report_query' => $request->report_query,
        'user_id' => auth()->id()
    ]);

    // نرجع الرابط كـ JSON ليقرأه الجافا سكريبت
    return response()->json([
        'success' => true,
        'pdf_url' => $response->json('pdf_url') // تأكد أن الاسم يطابق المخرج من n8n
    ]);
}
// public function generate(Request $request)
// {
//     $request->validate(['report_query' => 'required|string']);

//     $webhookUrl = 'https://n8n-production-7bdd.up.railway.app/webhook-test/reports';

//     // نرسل الطلب لـ n8n
//     $response = Http::post($webhookUrl, [
//         'report_query' => $request->report_query,
//         'user_id' => auth()->id()
//     ]);

//     // n8n الآن سيعيد لنا "محتوى الملف" (Binary) أو رابط مؤقت
//     // إذا كان n8n يعيد ملف PDF مباشرة:
//     $pdfContent = $response->body(); 
//     $fileName = 'report_' . time() . '.pdf';
//     $path = 'reports/' . $fileName;

//     // حفظ الملف في مجلد storage/app/public/reports
//     \Storage::disk('public')->put($path, $pdfContent);

//     // إرجاع الرابط العام للملف
//     return response()->json([
//         'success' => true,
//         'pdf_url' => asset('storage/' . $path) // هذا هو الرابط الذي سيستخدمه الـ iframe
//     ]);
// }
}
