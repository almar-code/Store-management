<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerSyncController extends Controller
{
    public function getCustomerIdBySupabase(Request $request)
    {
        $request->validate([
            'supabase_id' => 'required|string',
        ]);

        $customer = Customer::firstOrCreate(
            ['supabase_id' => $request->supabase_id],
            [
                'name' => $request->input('name', 'مستخدم جديد'),
                'email' => $request->input('email', $request->supabase_id . '@app.com'),
            ]
        );

        return response()->json([
            'status' => true,
            'customer_id' => $customer->customer_id,
        ], 200);
    }
}