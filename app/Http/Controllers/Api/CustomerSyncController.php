<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Cart; // <-- تأكد من استدعاء موديل السلة هنا
use Illuminate\Http\Request;

class CustomerSyncController extends Controller
{
    public function getCustomerIdBySupabase(Request $request)
    {
        $request->validate([
            'supabase_id' => 'required|string',
        ]);

        // جلب العميل أو إنشاؤه محلياً بناءً على الـ supabase_id
        $customer = Customer::firstOrCreate(
            ['supabase_id' => $request->supabase_id],
            [
                'name' => $request->input('name', 'مستخدم جديد'),
                'email' => $request->input('email', $request->supabase_id . '@app.com'),
            ]
        );

        // 🛒 إنشاء السلة فوراً هنا لضمان وجود سلة واحدة وحصرية لهذا العميل عند تسجيل الدخول
        Cart::firstOrCreate([
            'customer_id' => $customer->customer_id,
        ]);

        return response()->json([
            'status' => true,
            'customer_id' => $customer->customer_id,
        ], 200);
    }
}