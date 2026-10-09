<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;

class PermissionController extends Controller
{
    // عرض جميع الصلاحيات للمستخدمين
    public function Permissions()
    {
        try {
            $permissions = UserPermission::with('user', 'permission')->get();
            return view('Users.permission', compact('permissions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الصلاحيات');
        }
    }

    // صفحة إضافة صلاحية لمستخدم
    public function AddPermission($userID)
    {
        try {
            $permissions = Permission::all();
            $userName = User::findOrFail($userID)->full_name;
            return view('Users.addPermission', compact('userName', 'userID', 'permissions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب البيانات');
        }
    }

    // حفظ الصلاحية للمستخدم
    // public function store(Request $request, $userID)
    // {
    //     $request->validate([
    //         'userPermission' => 'required',
    //     ]);

    //     try {
    //         // تحقق إذا كانت الصلاحية موجودة بالفعل للمستخدم
    //         $exists = UserPermission::where('user_id', $userID)
    //             ->where('permission_id', $request->userPermission)
    //             ->first();

    //         if ($exists) {
    //             return redirect()->back()->with('error', 'الصلاحية موجودة مسبقاً لهذا المستخدم');
    //         }

    //         $userPermission =UserPermission::create([
    //             'user_id' => $userID,
    //             'permission_id' => $request->userPermission,
    //             'is_active' => $request->is_active,
    //         ]);
    //         // جلب بيانات المستخدم مع الصلاحية المقترنة للاستخدام في الأتمتة
    //         $userPermission->load('permission');
    //     $user = User::find($userID);
    //     $permissionName = $userPermission->permission->permission_name ?? 'موظف'; // تأكد من وجود علاقةpermission في الموديل أو جلبها يدوياً

    //     // ---------------- [ كود أتمتة n8n الجديد ] ----------------
    //     try {
    //         // سنقوم بإنشاء متغير بيئي جديد في Railway أو الـ .env المحلي باسم USER_WEBHOOK_URL
    //         $webhookUrl = 'https://n8n-production-7bdd.up.railway.app/webhook/welcome-new-user-webhook';

    //         if ($webhookUrl && $user) {
    //             // إرسال البيانات الأساسية إلى n8n
    //             Http::post($webhookUrl, [
    //                 'phone'           => $user->address, // افترضنا أنك تخزن رقم الهاتف في حقل الـ address أو استبدله بحقل الهاتف الفعلي
    //                 'full_name'       => $user->full_name,
    //                 'username'        => $user->username,
    //                 'plain_password'  =>  'تواصل مع مدير النظام ليجلب لك كلمة السر 779271679', // إذا كنت تريد تمريرها من الفورم يدوياً أو تركها سرية
    //                 'permission_name' => $permissionName,
    //                 'created_at'      => now()->format('Y-m-d H:i'),
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('فشل إرسال بيانات المستخدم إلى n8n: ' . $e->getMessage());
    //     }
    //     // --------------------------------------------------------
    //         return redirect()->back()->with('success', 'تم إضافة الصلاحية بنجاح');

    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الصلاحية');
    //     }
    // }
    public function store(Request $request, $userID, WhatsAppService $whatsAppService)
{
    $request->validate([
        'userPermission' => 'required',
    ]);

    try {
        // 1. التحقق مما إذا كانت الصلاحية موجودة بالفعل للمستخدم
        $exists = UserPermission::where('user_id', $userID)
            ->where('permission_id', $request->userPermission)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'الصلاحية موجودة مسبقاً لهذا المستخدم');
        }

        // 2. إضافة الصلاحية الجديدة للمستخدم
        $userPermission = UserPermission::create([
            'user_id'       => $userID,
            'permission_id' => $request->userPermission,
            'is_active'     => $request->is_active ?? 1,
        ]);

        // 3. جلب بيانات المستخدم والصلاحية المقترنة
        $userPermission->load('permission');
        $user = User::find($userID);
        $permissionName = $userPermission->permission->permission_name ?? 'موظف';

        // ---------------- [ إرسال إشعار الواتساب المباشر ] ----------------
        if ($user && !empty($user->phone)) {
            $fullName      = $user->full_name;
            $username      = $user->username;
            $joinDate      = now()->format('Y-m-d H:i');
            $loginUrl      = 'https://nice-store.alwaysdata.net/login';
            $plainPassword = 'تواصل مع مُدِيري النظام للحصول على كلمة السر 779271679 , 733357396';

            // صياغة النص المطلوب
            $message  = "اهلا عزيزي الموظف: {$fullName}\n";
            $message .= "\n";
            $message .= "يسعدنا إعلامك بأنه تم إضافتك بنجاح إلى منصة إدارة المتجر الذكي.\n";
            $message .= "\n";
            $message .= "تفاصيل الحساب الوظيفي الخاص بك:\n";
            $message .= "----------------------------------\n";
            $message .= "\n";
            $message .= "الاسم الكامل: {$fullName}\n";
            $message .= "\n";
            $message .= "تاريخ الانضمام: {$joinDate}\n";
            $message .= "\n";
            $message .= "الصلاحية الممنوحة: {$permissionName}\n";
            $message .= "\n";
            $message .= "اسم المستخدم: {$username}\n";
            $message .= "\n";
            $message .= "كلمة السر: {$plainPassword}\n";
            $message .= "\n";
            $message .= "رابط المنصة المباشر:\n";
            $message .= "{$loginUrl}\n";
            $message .= "----------------------------------\n";
            $message .= "نتمنى لك رحلة عمل ممتعة ومليئة بالإنجازات.";

            // إرسال الرسالة إلى رقم هاتف المستخدم عبر خدمة UltraMsg
            $whatsAppService->sendMessage($user->phone, $message);
        }
        // -----------------------------------------------------------------

        return redirect()->back()->with('success', 'تم إضافة الصلاحية بنجاح وإرسال الإشعار للواتساب');

    } catch (\Exception $e) {
        Log::error('خطأ أثناء إضافة الصلاحية أو إرسال الواتساب: ' . $e->getMessage());
        return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الصلاحية');
    }
}

    // عند الضغط على تعديل صلاحية
    public function edit($userID, $permissionID)
    {
        try {
            $editPermission = UserPermission::where('user_id', $userID)
                ->where('permission_id', $permissionID)
                ->firstOrFail();

            $permissions = Permission::all();
            $userName = User::findOrFail($userID)->full_name;

            return view('Users.AddPermission', compact('editPermission', 'permissions', 'userName'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'الصلاحية غير موجودة');
        }
    }

    // تحديث الصلاحية
    public function update(Request $request, $userID, $permissionID)
    {
        
        try {
            // احصل على السجل القديم
            $old = UserPermission::where('user_id', $userID)
                ->where('permission_id', $permissionID)
                ->firstOrFail();

            // إذا تم تغيير permission_id
            if ($request->permission_id != $permissionID) {
                // حذف السجل القديم
                UserPermission::where('user_id', $userID)
                    ->where('permission_id', $permissionID)
                    ->delete();

                // إنشاء سجل جديد بالقيم الجديدة
                UserPermission::create([
                    'user_id' => $userID,
                    'permission_id' => $request->userPermission,
                    'is_active' => $request->is_active,
                ]);
            } else {
                // إذا لم يتغير، فقط حدث النشاط
                $old->update([
                    'is_active' => $request->has('is_active') ? 1 : 0
                ]);
            }

            return redirect('/permission')->with('success', 'تم تحديث الصلاحية بنجاح');
        } catch (\Exception $e) {
            // return redirect()->back()->with('error', $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الصلاحية');
        }
    }
    
    public function updateStatus(Request $request, $userID, $permissionID)
{
    try {
        $old = UserPermission::where('user_id', $userID)
            ->where('permission_id', $permissionID)
            ->firstOrFail();

        $old->update([
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect('/permission')->with('success', 'تم تحديث الصلاحية بنجاح');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الصلاحية');
    }
}

    // حذف الصلاحية من المستخدم
    public function destroy($userID, $permissionID)
    {
        try {
            UserPermission::where('user_id', $userID)
                    ->where('permission_id', $permissionID)
                    ->delete();

            return redirect()->back()->with('success', 'تم حذف الصلاحية بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الصلاحية');
        }
    }
}
