<?php

namespace App\Services;

use App\Models\BackupRestore;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Backup\BackupDestination\BackupDestination;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class BackupRestoreService
{
    // جداول لا يلمسها الاسترجاع
    public const KEEP_TABLES = ['backup_settings', 'backup_restores', 'sessions', 'cache_locks'];

    // جداول لازم تكون موجودة في النسخة
    private const REQUIRED_TABLES = ['migrations', 'users', 'products'];

    private ?string $workDir = null;

    // البحث عن النسخة في السيرفر ثم في السحابة
    public function find(string $file): ?array
    {
        foreach (config('backup.backup.destination.disks') as $disk) {
            try {
                $backup = BackupDestination::create($disk, config('backup.backup.name'))
                    ->backups()
                    ->first(fn ($b) => basename($b->path()) === $file);
            } catch (Throwable) {
                continue;
            }

            if ($backup) {
                return ['disk' => $disk, 'path' => $backup->path(), 'date' => $backup->date()];
            }
        }

        return null;
    }

    // أسماء ملفات النسخ الموجودة على السيرفر
    public function localFiles(): array
    {
        try {
            $disk = config('backup.backup.destination.disks')[0];

            return BackupDestination::create($disk, config('backup.backup.name'))
                ->backups()
                ->map(fn ($b) => basename($b->path()))
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    // عدد البيانات اللي انضافت بعد تاريخ النسخة وراح تضيع
    public function impact(CarbonInterface $date): array
    {
        $since = $date->copy()->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
        $tables = ['orders' => 'طلب', 'customers' => 'عميل', 'products' => 'منتج'];
        $result = [];

        foreach ($tables as $table => $label) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'created_at')) {
                $result[] = [
                    'label' => $label,
                    'count' => DB::table($table)->where('created_at', '>', $since)->count(),
                ];
            }
        }

        return $result;
    }

    // تجهيز النسخة: تنزيل وفك تشفير وفحص
    public function prepare(string $file): string
    {
        $found = $this->find($file);

        if (! $found) {
            throw new RuntimeException('النسخة غير موجودة على السيرفر ولا في السحابة');
        }

        $zipPath = $this->workDir() . '/' . Str::random(8) . '.zip';

        // نسخ الملف محلياً، وينزل من السحابة لو لزم
        $in = Storage::disk($found['disk'])->readStream($found['path']);
        $out = fopen($zipPath, 'wb');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);

        return $this->filter($this->extract($zipPath));
    }

    // فك ملف ZIP المشفر واستخراج ملف SQL
    private function extract(string $zipPath): string
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('ملف النسخة تالف ولا يمكن فتحه');
        }

        if ($password = config('backup.backup.password')) {
            $zip->setPassword($password);
        }

        $entry = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (str_ends_with($name, '.sql') || str_ends_with($name, '.sql.gz')) {
                $entry = $name;
                break;
            }
        }

        if (! $entry) {
            $zip->close();
            throw new RuntimeException('النسخة لا تحتوي على ملف قاعدة بيانات');
        }

        $target = $this->workDir() . '/extract-' . Str::random(6);
        $ok = $zip->extractTo($target, $entry);
        $zip->close();

        if (! $ok) {
            throw new RuntimeException('تعذر فك النسخة، تأكد من كلمة سر التشفير');
        }

        $path = $target . '/' . $entry;

        // فك ضغط gzip لو موجود
        if (str_ends_with($path, '.gz')) {
            $plain = substr($path, 0, -3);
            $gz = gzopen($path, 'rb');
            $outFile = fopen($plain, 'wb');

            while (! gzeof($gz)) {
                fwrite($outFile, gzread($gz, 1048576));
            }

            gzclose($gz);
            fclose($outFile);
            $path = $plain;
        }

        return $path;
    }

    // فحص الجداول وحذف أوامر الجداول المحمية من الملف
    private function filter(string $sqlPath): string
    {
        $outPath = $this->workDir() . '/restore-' . Str::random(6) . '.sql';
        $in = fopen($sqlPath, 'rb');
        $out = fopen($outPath, 'wb');
        $keep = array_flip(self::KEEP_TABLES);
        $found = [];
        $skip = null;

        while (($line = fgets($in)) !== false) {
            // تخطي باقي أمر CREATE لجدول محمي
            if ($skip === 'create') {
                if (preg_match('/^\).*;\s*$/', $line)) {
                    $skip = null;
                }
                continue;
            }

            // تخطي بيانات جدول محمي
            if ($skip === 'lock') {
                if (str_starts_with($line, 'UNLOCK TABLES;')) {
                    $skip = null;
                }
                continue;
            }

            if (preg_match('/^CREATE TABLE `([^`]+)`/', $line, $m)) {
                $found[$m[1]] = true;

                if (isset($keep[$m[1]])) {
                    if (! preg_match('/;\s*$/', $line)) {
                        $skip = 'create';
                    }
                    continue;
                }
            } elseif (preg_match('/^(DROP TABLE IF EXISTS|INSERT INTO) `([^`]+)`/', $line, $m) && isset($keep[$m[2]])) {
                continue;
            } elseif (preg_match('/^LOCK TABLES `([^`]+)` WRITE;/', $line, $m) && isset($keep[$m[1]])) {
                $skip = 'lock';
                continue;
            }

            fwrite($out, $line);
        }

        fclose($in);
        fclose($out);

        $missing = array_diff(self::REQUIRED_TABLES, array_keys($found));

        if ($missing) {
            throw new RuntimeException('النسخة ناقصة ولا تحتوي على الجداول: ' . implode('، ', $missing));
        }

        return $outPath;
    }

    // نسخة أمان من البيانات الحالية قبل الاسترجاع
    public function safetyBackup(): string
    {
        $file = 'pre-restore-' . now()->format('Y-m-d-H-i-s') . '.zip';

        Artisan::call('backup:run', [
            '--only-db' => true,
            '--filename' => $file,
            '--disable-notifications' => true,
        ]);

        if (! $this->find($file)) {
            throw new RuntimeException('تعذر إنشاء نسخة الأمان، تم إلغاء الاسترجاع ولم تتغير البيانات');
        }

        return $file;
    }

    // استيراد ملف SQL في قاعدة البيانات
    public function import(string $sqlPath): void
    {
        $c = config('database.connections.' . config('database.default'));
        $bin = rtrim((string) ($c['dump']['dump_binary_path'] ?? ''), '/\\');
        $mysql = $bin === '' ? 'mysql' : $bin . '/mysql';

        // بيانات الاتصال في ملف مؤقت بدل سطر الأوامر
        $esc = fn ($v) => addcslashes((string) $v, '\\"');
        $cnf = $this->workDir() . '/client.cnf';
        $options = "[client]\nuser=\"{$esc($c['username'])}\"\npassword=\"{$esc($c['password'])}\"\nhost=\"{$esc($c['host'])}\"\nport={$c['port']}\n";

        if (! empty($c['unix_socket'])) {
            $options .= "socket=\"{$esc($c['unix_socket'])}\"\n";
        }

        file_put_contents($cnf, $options);
        @chmod($cnf, 0600);

        $input = fopen($sqlPath, 'rb');

        $process = new Process([
            $mysql,
            '--defaults-extra-file=' . $cnf,
            '--default-character-set=utf8mb4',
            $c['database'],
        ]);
        $process->setTimeout(1800);
        $process->setInput($input);
        $process->run();

        if (is_resource($input)) {
            fclose($input);
        }

        if (! $process->isSuccessful()) {
            throw new RuntimeException('فشل استيراد النسخة: ' . Str::limit(trim($process->getErrorOutput()), 400));
        }
    }

    // سجلات migrations الخاصة بالجداول المحمية
    public function protectedMigrations(): array
    {
        return DB::table('migrations')
            ->where('migration', 'like', '%create_backup_%')
            ->pluck('migration')
            ->all();
    }

    // إعادة سجلاتها بعد الاسترجاع حتى لا يحاول migrate إنشاءها مرة ثانية
    public function restoreMigrations(array $names): void
    {
        $batch = (int) DB::table('migrations')->max('batch');

        foreach ($names as $name) {
            if (! DB::table('migrations')->where('migration', $name)->exists()) {
                DB::table('migrations')->insert(['migration' => $name, 'batch' => $batch]);
            }
        }
    }

    // تحديث هيكل الجداول لو النسخة أقدم من الكود
    public function migrate(): ?string
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return null;
        } catch (Throwable $e) {
            return 'تم الاسترجاع، لكن تعذر تحديث بعض الجداول: ' . Str::limit($e->getMessage(), 200);
        }
    }

    // إيميل للمدير بعد كل عملية استرجاع
    public function notify(?BackupRestore $restore): void
    {
        $to = config('backup.notifications.mail.to');

        if (! $restore || ! $to) {
            return;
        }

        $status = $restore->statusInfo()['label'];

        $body = 'عملية استرجاع نسخة احتياطية في ' . config('app.name') . "\n\n"
            . "النتيجة: {$status}\n"
            . "النسخة: {$restore->backup_file}\n"
            . 'بواسطة: ' . $restore->userName() . "\n"
            . 'الوقت: ' . now()->format('Y-m-d h:i A') . "\n"
            . ($restore->safety_file ? "نسخة الأمان: {$restore->safety_file}\n" : '')
            . "\n{$restore->message}";

        try {
            Mail::raw($body, fn ($m) => $m->to($to)->subject('استرجاع نسخة احتياطية - ' . $status));
        } catch (Throwable) {
            // فشل الإيميل لا يوقف العملية
        }
    }

    // حذف الملفات المؤقتة
    public function cleanup(): void
    {
        if ($this->workDir) {
            File::deleteDirectory($this->workDir);
            $this->workDir = null;
        }
    }

    private function workDir(): string
    {
        if (! $this->workDir) {
            $this->workDir = storage_path('app/restore-temp/' . Str::random(16));
            File::ensureDirectoryExists($this->workDir);
        }

        return $this->workDir;
    }
}
