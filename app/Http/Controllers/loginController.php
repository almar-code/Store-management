<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function Login() {
        return view('Login.login');
    }

    public function Examine(Request $request)
    {
        // التحقق من المدخلات
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        try {
            // البحث عن المستخدم باستخدام اليوزر نيم
            $user = User::where('username', $request->username)->first();

            // المطابقة اليدوية: كلمة السر من الفورم مقابل password_hash من القاعدة
            if ($user && Hash::check($request->password, $user->password_hash)) {
            // التحقق من حالة المستخدم في جدول user_permissions
                $userPermission = $user->userPermissions()->where('is_active', 1)->first();

                if (!$userPermission) {
                    return back()->with('error', 'حسابك غير نشط، يرجى التواصل مع الإدارة')->withInput();
                }

                // تسجيل الدخول في النظام
                Auth::login($user);
                $request->session()->regenerate();

                // التحويل لصفحة المستخدمين
                return redirect('/')->with('success', 'تم تسجيل الدخول بنجاح');
            }

            // في حال فشل البيانات
            return back()->with('error', 'خطأ في اسم المستخدم أو كلمة المرور')->withInput();

        } catch (\Throwable $th) {
            return back()->with('error', 'حدث خطأ غير متوقع في النظام');
        }
    }

    public function VerifyTelegramUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password_hash)) {
            return response()->json([
                'success' => false,
                'authenticated' => false,
                'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة',
            ], 401);
        }

        $permissions = $user->userPermissions()
            ->with('permission')
            ->get()
            ->map(function ($userPermission) {
                return [
                    'permission_name' => $userPermission->permission->permission_name ?? null,
                    'is_active' => (bool) $userPermission->is_active,
                    'status' => $userPermission->is_active ? 'نشطة' : 'غير نشطة',
                ];
            })
            ->filter(fn($permission) => $permission['permission_name'] !== null)
            ->values();

        $hasActivePermission = $permissions->contains('is_active', true);

        return response()->json([
            'success' => true,
            'authenticated' => $hasActivePermission,
            'username' => $user->username,
            'permissions' => $permissions,
            'message' => $hasActivePermission
                ? 'تم التحقق من المستخدم بنجاح'
                : 'الحساب غير نشط، لا توجد صلاحيات نشطة',
        ], $hasActivePermission ? 200 : 403);
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout(); // مسح بيانات المستخدم من السيرفر
            $request->session()->invalidate(); // إبطال الجلسة الحالية
            $request->session()->regenerateToken(); // تغيير توكن الحماية CSRF

            return redirect('/login'); // العودة للوجن
        } catch (\Throwable $th) {
            return back()->with('error', 'حدث خطأ غير متوقع في النظام');
        }
    }
}