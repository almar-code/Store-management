<?php

namespace App\Http\Controllers;

use App\Jobs\RunRestoreJob;
use App\Models\BackupRestore;
use App\Services\BackupRestoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BackupRestoreController extends Controller
{
    public function __construct(private BackupRestoreService $service)
    {
    }

    // ===== صفحة تأكيد الاسترجاع =====
    public function confirm(Request $request, string $file)
    {
        $found = $this->service->find($file);
        abort_unless($found, 404);

        $date = $found['date']->copy()->timezone(config('app.timezone'));
        $impact = $this->service->impact($found['date']);
        $undo = $request->filled('undo') ? BackupRestore::find($request->integer('undo')) : null;
        $running = BackupRestore::whereIn('status', ['pending', 'running'])->exists();
        $onCloud = $found['disk'] !== config('backup.backup.destination.disks')[0];

        return view('Backups.restorebackup', compact('file', 'date', 'impact', 'undo', 'running', 'onCloud'));
    }

    // ===== بدء الاسترجاع بعد التحقق =====
    public function store(Request $request, string $file)
    {
        $request->validate([
            'password' => 'required|string',
            'confirm_text' => 'required|in:استرجاع',
            'undo_of' => 'nullable|integer|exists:backup_restores,id',
        ], [
            'confirm_text.in' => 'اكتب كلمة "استرجاع" بالضبط للتأكيد',
        ], [
            'password' => 'كلمة المرور',
            'confirm_text' => 'كلمة التأكيد',
        ]);

        // التحقق من كلمة مرور المدير
        if (! Hash::check($request->password, auth()->user()->password_hash)) {
            return back()
                ->withErrors(['password' => 'كلمة المرور غير صحيحة'])
                ->withInput($request->except('password'));
        }

        abort_unless($this->service->find($file), 404);

        if (BackupRestore::whereIn('status', ['pending', 'running'])->exists()) {
            return back()->with('error', 'توجد عملية استرجاع جارية، انتظر حتى تنتهي');
        }

        $restore = BackupRestore::create([
            'user_id' => auth()->id(),
            'backup_file' => $file,
            'undo_of' => $request->input('undo_of'),
            'status' => 'pending',
            'message' => 'بانتظار البدء',
        ]);

        RunRestoreJob::dispatchAfterResponse($restore->id);

        return redirect('backup-restores/' . $restore->id);
    }

    // ===== صفحة متابعة العملية ونتيجتها =====
    public function show(BackupRestore $restore)
    {
        $restore->load('user');
        $canUndo = $restore->status === 'success'
            && $restore->safety_file
            && in_array($restore->safety_file, $this->service->localFiles());

        return view('Backups.restorestatus', compact('restore', 'canUndo'));
    }

    // ===== سجل عمليات الاسترجاع =====
    public function index()
    {
        $restores = BackupRestore::with('user')->latest()->get();
        $files = array_flip($this->service->localFiles());

        $stats = [
            'total' => $restores->count(),
            'success' => $restores->where('status', 'success')->count(),
            'failed' => $restores->where('status', 'failed')->count(),
            'undone' => $restores->where('status', 'undone')->count(),
        ];

        return view('Backups.restorelog', compact('restores', 'files', 'stats'));
    }
}
