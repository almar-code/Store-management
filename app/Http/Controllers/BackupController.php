<?php

namespace App\Http\Controllers;

use App\Jobs\RunBackupJob;
use App\Models\BackupSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestination;

class BackupController extends Controller
{
    /*
     * أنواع النسخ — كل نوع له صفحة خاصة في القائمة الجانبية
     * field : عمود مدة الاحتفاظ في جدول backup_settings
     * group : تجميع النسخ (نسخة واحدة لكل يوم / أسبوع / شهر / سنة)
     */
    private const TYPES = [
        'daily'   => ['title' => 'اليومية',   'field' => 'keep_daily_days',     'unit' => 'يوم',   'group' => 'Y-m-d', 'sub' => 'subDays',   'icon' => 'bi-calendar-day'],
        'weekly'  => ['title' => 'الأسبوعية', 'field' => 'keep_weekly_weeks',   'unit' => 'أسبوع', 'group' => 'o-W',   'sub' => 'subWeeks',  'icon' => 'bi-calendar-week'],
        'monthly' => ['title' => 'الشهرية',   'field' => 'keep_monthly_months', 'unit' => 'شهر',   'group' => 'Y-m',   'sub' => 'subMonths', 'icon' => 'bi-calendar-month'],
        'yearly'  => ['title' => 'السنوية',   'field' => 'keep_yearly_years',   'unit' => 'سنة',   'group' => 'Y',     'sub' => 'subYears',  'icon' => 'bi-calendar3'],
    ];

    // القرص اللي تنحفظ فيه النسخ — أول قرص في config/backup.php
    private function diskName(): string
    {
        return config('backup.backup.destination.disks')[0];
    }

    // كل النسخ الموجودة، الأحدث أولاً
    private function backups()
    {
        return BackupDestination::create($this->diskName(), config('backup.backup.name'))->backups();
    }

    // تحويل الحجم لصيغة مقروءة (بدون الحاجة لإضافة intl)
    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        return round($bytes / 1024, 2) . ' KB';
    }

    // تحويل النسخة لبيانات جاهزة للعرض
    private function row($backup): array
    {
        $file = basename($backup->path());

        return [
            'file'   => $file,
            'date'   => $backup->date()->timezone(config('app.timezone')),
            'bytes'  => $backup->sizeInBytes(),
            'size'   => $this->formatSize($backup->sizeInBytes()),
            'manual' => str_starts_with($file, 'manual-'),
        ];
    }

    // ===== صفحة إنشاء نسخة احتياطية =====
    public function create()
    {
        $settings = BackupSetting::current();
        $all = $this->backups();

        $stats = [
            'count' => $all->count(),
            'size'  => $this->formatSize($all->sum(fn ($b) => $b->sizeInBytes())),
        ];

        // أحدث نسخة — لعرض زر تحميلها مباشرة بعد إنشائها
        $latest = $all->first() ? $this->row($all->first()) : null;

        return view('Backups.addbackups', compact('settings', 'stats', 'latest'));
    }

    // زر "إنشاء نسخة الآن" — يعمل بعد إرسال الصفحة فلا ينتظر المدير
    public function store()
    {
        RunBackupJob::dispatchAfterResponse();

        return redirect('addbackups')->with('success', 'بدأ إنشاء النسخة الاحتياطية، ستظهر خلال دقيقة');
    }

    // ===== صفحات عرض النسخ (يومية / أسبوعية / شهرية / سنوية) =====
    public function index(string $type)
    {
        $meta = self::TYPES[$type];
        $settings = BackupSetting::current();
        $keep = (int) $settings->{$meta['field']};
        $from = now()->{$meta['sub']}($keep);
        $tz = config('app.timezone');

        $backups = $this->backups()->filter(fn ($b) => $b->date()->gte($from));

        if ($type === 'daily') {
            // اليومية: كل النسخ (التلقائية واليدوية) عشان أي نسخة يدوية تقدر تتحمّل
            $backups = $backups->values();
        } else {
            // الأسبوعية / الشهرية / السنوية: أحدث نسخة في كل أسبوع / شهر / سنة
            $backups = $backups
                ->groupBy(fn ($b) => $b->date()->timezone($tz)->format($meta['group']))
                ->map(fn ($group) => $group->first())
                ->values();
        }

        $backups = $backups->map(fn ($b) => $this->row($b));

        $stats = [
            'count'  => $backups->count(),
            'keep'   => $keep,
            'latest' => $backups->first()['date'] ?? null,
            'size'   => $this->formatSize($backups->sum('bytes')),
        ];

        return view('Backups.backups', compact('type', 'meta', 'backups', 'stats'));
    }

    // ===== صفحة إعدادات النسخ =====
    public function settings()
    {
        $settings = BackupSetting::current();
        $types = self::TYPES;

        return view('Backups.backupsettings', compact('settings', 'types'));
    }

    // حفظ الإعدادات
    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'enabled' => 'required|boolean',
            'run_time' => 'required|date_format:H:i',
            'keep_daily_days' => 'required|integer|min:1|max:90',
            'keep_weekly_weeks' => 'required|integer|min:1|max:52',
            'keep_monthly_months' => 'required|integer|min:1|max:60',
            'keep_yearly_years' => 'required|integer|min:1|max:20',
        ], [], [
            'enabled' => 'حالة النسخ التلقائي',
            'run_time' => 'وقت النسخ',
            'keep_daily_days' => 'مدة النسخ اليومية',
            'keep_weekly_weeks' => 'مدة النسخ الأسبوعية',
            'keep_monthly_months' => 'مدة النسخ الشهرية',
            'keep_yearly_years' => 'مدة النسخ السنوية',
        ]);

        BackupSetting::current()->update($data);

        return redirect('backupsettings')->with('success', 'تم حفظ إعدادات النسخ الاحتياطي بنجاح');
    }

    // تحميل نسخة — يُسمح فقط بملف موجود فعلاً ضمن النسخ
    public function download(string $file)
    {
        $backup = $this->backups()->first(fn ($b) => basename($b->path()) === $file);

        abort_unless($backup, 404);

        return Storage::disk($this->diskName())->download($backup->path());
    }
}
