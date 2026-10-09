<?php

namespace App\Http\Controllers;
use App\Mail\SendUserCredentials;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\WhatsAppService;
class UserController extends Controller
{
      public function AddUser(){
        try {
        return view('Users.addUser');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء فتح الصفحة');    
        }
    }
 public function store(Request $request, WhatsAppService $whatsAppService)
    {
        try {
            // Step 1: التحقق من البيانات المدخلة
            $request->validate([
                'userFullName'  => 'required|max:255',
                'userName'      => 'required|max:255',
                'password_hash' => 'required|max:255',
                'email'         => 'required|email|max:255',
                'phone'         => 'required|max:20',
                'userAddress'   => 'required|max:255',
            ]);

            // Step 2: التحقق من عدم تكرار الحقول الفريدة
            if (User::where('email', $request->email)->exists()) {
                return back()->withInput()->with('error', 'البريد الإلكتروني مستخدم من قبل');
            }

            if (User::where('username', $request->userName)->exists()) {
                return back()->withInput()->with('error', 'اسم المستخدم مستخدم من قبل');
            }

            if (User::where('phone', $request->phone)->exists()) {
                return back()->withInput()->with('error', 'رقم الهاتف مستخدم من قبل');
            }

            // Step 3: إنشاء المستخدم في قاعدة البيانات
            $user = User::create([
                'full_name'     => $request->userFullName,
                'username'      => $request->userName,
                'password_hash' => Hash::make($request->password_hash),
                'email'         => $request->email,
                'phone'         => $request->phone,
                'address'       => $request->userAddress,
            ]);

            // Step 4: صياغة رسالة الواتساب الترحيبية
            $plainPassword = $request->password_hash; // كلمة المرور قبل التشفير
            
            $message  = "مرحباً بك {$request->userFullName} 👋\n\n";
            $message .= "تم إنشاء حسابك في المنصة بنجاح.\n";
            $message .= "---------------------------\n";
            $message .= "👤 اسم المستخدم: {$request->userName}\n";
            $message .= "🔑 كلمة المرور: {$plainPassword}\n";
            $message .= "---------------------------\n";
            $message .= "يرجى الاحتفاظ بهذه البيانات وتغيير كلمة المرور بعد التسجيل الأول.";

            // Step 5: إرسال الرسالة
            $whatsAppService->sendMessage($request->phone, $message);

            return redirect()->back()->with('success', 'تم إضافة المستخدم بنجاح وإرسال بيانات الحساب عبر الواتساب');

            } catch (\Exception $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'حدث خطأ أثناء إضافة المستخدم');
            }
    }
    public function edit($id)
    {
        try {

            $edituser = User::findOrFail($id);

            return view('Users.addUser', compact('edituser'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'القسم غير موجود');

        }
    }
    public function update(Request $request, $id)
        {
            try {

                $user = User::findOrFail($id);

                $user->full_name = $request->userFullName;
                $user->username = $request->userName;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->address = $request->userAddress;

                // إذا كتب كلمة سر جديدة
                if ($request->filled('password_hash')) {
                    $user->password_hash = Hash::make($request->password_hash);
                }
                $user->save();
                return redirect('/users')->with('success', 'تم تعديل المستخدم بنجاح');

            } catch (\Throwable $th) {
                return redirect('/users')->with('error', 'حدث خطاء اثناء  تعديل المستخدم ');
                
            }
        }



    public function Users(){
        try {

            $users = User::with('userPermissions')->get();
            // $users =User::all();
            return view('Users.users', compact('users'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب المستخدمين');
        }
    }

    public function destroy($id)
    {
        try {
            $user =User::findOrFail($id);
            $user->delete();
            return redirect()->back()->with('success', 'تم حذف المستخدم ');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطاء اثناء  حذف المستخدم ');
        }
    }

    public function AddPermission(){
        return view('Users.AddPermission', []);
    }
    public function Permission(){
        return view('Users.permission', []);
    }
}
