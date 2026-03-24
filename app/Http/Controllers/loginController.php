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