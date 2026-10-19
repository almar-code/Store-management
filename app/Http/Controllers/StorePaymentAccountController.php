<?php

namespace App\Http\Controllers;

use App\Models\StorePaymentAccount;
use Illuminate\Http\Request;
use App\Services\TranslationService; // تأكد من استدعاء خدمة الترجمة حسب المسار لديك

class StorePaymentAccountController extends Controller
{
    // عرض القائمة (تأكد أن اسم الملف paymentaccounts الموجود داخل مجلد Storepaymentaccounts)
    public function index()
    {
        $accounts = StorePaymentAccount::all();
        return view('Storepaymentaccounts.paymentaccounts', compact('accounts'));
    }

    // عرض صفحة الإضافة
    public function create()
    {
        return view('Storepaymentaccounts.addpaymentaccounts');
    }

    // حفظ البيانات مع الترجمة التلقائية لاسم المزود
    public function store(Request $request)
    {
        $request->validate([
            'account_type' => 'required|in:wallet,bank',
            'provider_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        // الترجمة التلقائية لاسم المزود إلى الإنجليزية
        $providerNameEn = TranslationService::translate($request->provider_name);

        $data = $request->all();
        $data['provider_name_en'] = $providerNameEn;

        StorePaymentAccount::create($data);

        return redirect('store_payment_accounts')->with('success', 'تم إضافة حساب الدفع بنجاح');
    }

    // عرض صفحة التعديل
    public function edit($id)
    {
        $account = StorePaymentAccount::findOrFail($id);
        return view('Storepaymentaccounts.addpaymentaccounts', compact('account'));
    }

    // تحديث البيانات مع الترجمة التلقائية
    public function update(Request $request, $id)
    {
        $request->validate([
            'account_type' => 'required|in:wallet,bank',
            'provider_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $providerNameEn = TranslationService::translate($request->provider_name);

        $account = StorePaymentAccount::findOrFail($id);
        
        $data = $request->all();
        $data['provider_name_en'] = $providerNameEn;

        $account->update($data);

        return redirect('store_payment_accounts')->with('success', 'تم تعديل حساب الدفع بنجاح');
    }

    public function updateactive($id)
    {
        try {
            $account = StorePaymentAccount::findOrFail($id);
            $account->is_active = !$account->is_active;
            $account->update();

            $msg = $account->is_active ? 'تم تفعيل حالة الحساب' : 'تم إلغاء تفعيل حالة الحساب';
            return redirect()->back()->with('success', $msg);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل');
        }
    }

    // الحذف
    public function destroy($id)
    {
        $account = StorePaymentAccount::findOrFail($id);
        $account->delete();

        return redirect('store_payment_accounts')->with('success', 'تم حذف حساب الدفع بنجاح');
    }
}