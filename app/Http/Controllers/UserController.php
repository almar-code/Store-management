<?php

namespace App\Http\Controllers;
use App\Mail\SendUserCredentials;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
      public function AddUser(){
        try {
        return view('Users.addUser');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء فتح الصفحة');    
        }
    }
    public function store(Request $request)
    {
        
        try {
            $existingUser = User::where('email', $request->email)->first();
if ($existingUser) {
    return back()->with('error', 'البريد الإلكتروني مستخدم من قبل');
}

             $request->validate([
            'userFullName' => 'required|max:255',
            'userName' => 'required|max:255',
            'password_hash' =>  'required|max:255',
            'email' => 'required|max:255',
            'userAddress' => 'required|max:255',
        ]);
         $plainPassword = $request->password_hash;
        $user = User::create([
            'full_name' => $request->userFullName,
            'username' => $request->userName,
            'password_hash' => Hash::make($request->password_hash), // تشفير كلمة السر
            'email' => $request->email,
            'address' => $request->userAddress,
        ]);
        // إرسال الإيميل
        Mail::to($user->email)->send(
            new SendUserCredentials($user->username, $plainPassword)
        );
            return redirect()->back()->with('success', 'تم إضافة المستخدم بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            // return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة المستخدم ');
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
                $user->address = $request->userAddress;

                // إذا كتب كلمة سر جديدة
                if ($request->filled('password_hash')) {
                    $user->password_hash = Hash::make($request->password_hash);
                }
                $user->save();
                return redirect('/users')->with('success', 'تم تعديل المستخدم بنجاح');

            } catch (\Throwable $th) {
                return redirect('/users')->with('success', 'حدث خطاء اثناء  تعديل المستخدم ');
                
            }
        }



    public function Users(){
        try {
            $users =User::all();
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
            return redirect()->back()->with('success', 'حدث خطاء اثناء  حذف المستخدم ');
        }
    }

    public function AddPermission(){
        return view('Users.AddPermission', []);
    }
    public function Permission(){
        return view('Users.permission', []);
    }
}
