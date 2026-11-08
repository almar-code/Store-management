<?php

namespace App\Http\Controllers;

use App\Models\ReportRequest;
use App\Models\SavedReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Throwable;

class ReportController extends Controller
{
    // التقارير غير المحفوظة (تُحذف تلقائياً بعد يوم)
    private const TEMP_DIR = 'reports/temp';

    // التقارير المحفوظة
    private const SAVED_DIR = 'reports/saved';

    // عدد الطلبات السابقة المعروضة تحت حقل الإدخال
    private const RECENT_REQUESTS_LIMIT = 15;

    private const STORE_NAME = 'Nlice abayas';
    private const LOGO_URL = 'https://nice-store.alwaysdata.net/assets/img/logo.png';

    // صفحة التقارير المحفوظة
    public function index()
    {
        $reports = SavedReport::latest()->paginate(12);

        return view('Reports.reports', compact('reports'));
    }

    // صفحة التقرير المخصص
    public function custom()
    {
        $recentRequests = ReportRequest::where('user_id', auth()->id())
            ->latest('updated_at')
            ->take(self::RECENT_REQUESTS_LIMIT)
            ->get();

        return view('Reports.customReport', compact('recentRequests'));
    }

    // صفحة التقارير الجاهزة
    public function ready()
    {
        return view('Reports.reportsReady');
    }

    // إنشاء تقرير: n8n يرجع HTML و Laravel يحوله إلى PDF
    public function generate(Request $request)
    {
        $request->validate([
            'report_query' => 'required|string|max:2000',
            'remember'     => 'nullable|boolean',
        ]);

        $this->cleanTempReports();

        try {
            $response = Http::timeout(120)->post(
                config('services.n8n.reports_webhook'),
                [
                    'report_query' => $request->report_query,
                    'user_id'      => auth()->id(),
                ]
            );

            $html = $response->json('html');

            if (!$response->successful() || empty($html)) {
                Log::warning('n8n report failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم استلام محتوى التقرير، حاول مرة أخرى',
                ], 502);
            }

            // تنظيف علامات markdown
            $html = preg_replace('/```(html)?/i', '', $html);

            // التاريخ من الخادم
            $html = str_replace(
                '__REPORT_DATE__',
                now('Asia/Aden')->format('Y/m/d - H:i'),
                $html
            );

            // استخراج العنوان وبناء الهيدر بالترتيب الصحيح
            preg_match('/class="report-title"[^>]*>(.*?)<\/td>/su', $html, $m);
            $title = trim(strip_tags($m[1] ?? '')) ?: 'تقرير المتجر';

            $html = preg_replace(
                '/<div class="header">.*?<\/div>/su',
                $this->buildHeader($title),
                $html,
                1
            );

            // حفظ الملف مؤقتاً
            $path = self::TEMP_DIR . '/report_' . now()->format('Ymd_His') . '_' . Str::random(8) . '.pdf';

            Storage::disk('public')->put($path, $this->renderPdf($html, $title));

            // حفظ نص الطلب في قائمة الطلبات السابقة (من صفحة التقرير المخصص فقط)
            $savedRequest = $request->boolean('remember')
                ? $this->rememberRequest($request->report_query)
                : null;

            return response()->json([
                'success' => true,
                'title'   => $title,
                'file'    => basename($path),
                'pdf_url' => asset('storage/' . $path),
                'request' => $savedRequest ? [
                    'id'    => $savedRequest->request_id,
                    'query' => $savedRequest->report_query,
                ] : null,
            ]);
        } catch (Throwable $e) {
            Log::error('Report generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء التقرير',
            ], 500);
        }
    }

    // حفظ التقرير: نقله من المؤقت إلى المحفوظ وتسجيله في قاعدة البيانات
    public function save(Request $request)
    {
        $data = $request->validate([
            'file'         => ['required', 'string', 'regex:/^report_[A-Za-z0-9_]+\.pdf$/'],
            'title'        => 'required|string|max:255',
            'report_query' => 'required|string|max:2000',
        ]);

        $disk = Storage::disk('public');
        $tempPath = self::TEMP_DIR . '/' . $data['file'];
        $savedPath = self::SAVED_DIR . '/' . $data['file'];

        if (!$disk->exists($tempPath)) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية التقرير المؤقت، يرجى إنشاؤه من جديد',
            ], 404);
        }

        try {
            $disk->move($tempPath, $savedPath);

            $report = SavedReport::create([
                'title'        => $data['title'],
                'report_query' => $data['report_query'],
                'file_path'    => $savedPath,
                'user_id'      => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'id'      => $report->report_id,
                'pdf_url' => $report->pdf_url,
            ]);
        } catch (Throwable $e) {
            Log::error('Report save error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'تعذر حفظ التقرير',
            ], 500);
        }
    }

    // حذف تقرير محفوظ
    public function destroy($id)
    {
        try {
            $report = SavedReport::findOrFail($id);

            Storage::disk('public')->delete($report->file_path);
            $report->delete();

            return redirect()->route('reports.index')->with('success', 'تم حذف التقرير بنجاح');
        } catch (Throwable $e) {
            return redirect()->route('reports.index')->with('error', 'تعذر حذف التقرير');
        }
    }

    // حذف طلب سابق واحد
    public function destroyRequest($id)
    {
        try {
            ReportRequest::where('request_id', $id)
                ->where('user_id', auth()->id())
                ->delete();

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Report request delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'تعذر حذف الطلب',
            ], 500);
        }
    }

    // حذف كل الطلبات السابقة للمستخدم الحالي
    public function clearRequests()
    {
        try {
            ReportRequest::where('user_id', auth()->id())->delete();

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('Report requests clear error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'تعذر حذف الطلبات',
            ], 500);
        }
    }

    // تسجيل الطلب أو تحديث وقته إن كان مكرراً، بدون إيقاف التقرير لو فشل
    private function rememberRequest(string $query): ?ReportRequest
    {
        try {
            $query = trim($query);

            $item = ReportRequest::firstOrNew([
                'user_id'      => auth()->id(),
                'report_query' => $query,
            ]);

            $item->exists ? $item->touch() : $item->save();

            return $item;
        } catch (Throwable $e) {
            Log::warning('Report request remember failed: ' . $e->getMessage());

            return null;
        }
    }

    private function buildHeader(string $title): string
    {
        return '<div class="header"><table class="layout" style="width:100%;"><tr>'
            . '<td style="width:60%; text-align:right; font-size:20px; font-weight:bold;">' . e($title) . '</td>'
            . '<td style="width:32%; text-align:left; font-size:18px; font-weight:bold; color:#00695c; padding-left:10px;">' . self::STORE_NAME . '</td>'
            . '<td style="width:8%; text-align:left;"><img src="' . self::LOGO_URL . '" style="width:50px; height:50px;"></td>'
            . '</tr></table></div>';
    }

    private function renderPdf(string $html, string $title): string
    {
        $tempDir = storage_path('app/mpdf');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'directionality'   => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'default_font'     => 'dejavusans',
            'tempDir'          => $tempDir,
        ]);

        $mpdf->SetTitle($title);
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    // حذف التقارير المؤقتة التي مر عليها أكثر من يوم
    private function cleanTempReports(): void
    {
        try {
            $disk = Storage::disk('public');
            $limit = now()->subDay()->getTimestamp();

            foreach ($disk->files(self::TEMP_DIR) as $file) {
                if ($disk->lastModified($file) < $limit) {
                    $disk->delete($file);
                }
            }
        } catch (Throwable $e) {
            Log::warning('Temp reports cleanup failed: ' . $e->getMessage());
        }
    }
}