<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function Customers()
    {
        try {
            $customers = Customer::all();
            return view('Customers.customers', compact('customers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب العملاء');
        }
    }

    /**
     * استقبال بيانات المستخدم الفورية من Supabase عبر الـ Webhook
     */
    public function handleSupabaseWebhook(Request $request)
    {
        $record = $request->input('record');

        if (!$record || empty($record['id'])) {
            return response()->json(['message' => 'Invalid payload or missing ID'], 400);
        }

        Customer::updateOrCreate(
            ['supabase_id' => $record['id']],
            [
                'name'          => $record['user_name'] ?? 'Customer',
                'phone'         => $record['phone_number'] ?? null,
                'profile_image' => $record['avatar_url'] ?? null,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Customer synced successfully via Webhook',
        ]);
    }
}