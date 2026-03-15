<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Models\UserPermission;

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
            return view('Users.AddPermission', compact('userName', 'userID', 'permissions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب البيانات');
        }
    }

    // حفظ الصلاحية للمستخدم
    public function store(Request $request, $userID)
    {
        $request->validate([
            'userPermission' => 'required',
        ]);

        try {
            // تحقق إذا كانت الصلاحية موجودة بالفعل للمستخدم
            $exists = UserPermission::where('user_id', $userID)
                ->where('permission_id', $request->userPermission)
                ->first();

            if ($exists) {
                return redirect()->back()->with('error', 'الصلاحية موجودة مسبقاً لهذا المستخدم');
            }

            UserPermission::create([
                'user_id' => $userID,
                'permission_id' => $request->userPermission,
                'is_active' => $request->is_active,
            ]);

            return redirect()->back()->with('success', 'تم إضافة الصلاحية بنجاح');

        } catch (\Exception $e) {
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
