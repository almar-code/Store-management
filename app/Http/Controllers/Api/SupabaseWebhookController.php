<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\SupabaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupabaseWebhookController extends Controller
{
    public function profileUpdated(
        Request $request,
        SupabaseService $supabase
    ): JsonResponse {

        // التحقق من سر الـ Webhook
        $secret = $request->header('X-Webhook-Secret');

        if (
            empty($secret) ||
            !hash_equals(
                (string) config('services.supabase.webhook_secret'),
                $secret
            )
        ) {
            Log::warning(
                'Supabase Webhook unauthorized request.'
            );

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // تسجيل البيانات القادمة من Supabase
        $data = $request->all();

        Log::info(
            'Supabase profile webhook received',
            $data
        );

        // الحصول على سجل العميل
        $record = $data['record'] ?? null;

        if (!$record || empty($record['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Profile record is missing.',
            ], 422);
        }

        $supabaseId = $record['id'];

        // جلب بيانات المستخدم من Supabase Auth
        $user = $supabase->getUserById($supabaseId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Supabase user not found.',
            ], 404);
        }

        $email = $user['email'] ?? null;

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer email is missing.',
            ], 422);
        }

        // إنشاء العميل أو تحديثه
        $customer = Customer::updateOrCreate(
            [
                'supabase_id' => $supabaseId,
            ],
            [
                'name' => $record['user_name'] ?? 'Customer',
                'email' => $email,
                'phone' => $record['phone_number'] ?? null,
                'profile_image' => $record['avatar_url'] ?? null,
            ]
        );

        Log::info(
            'Customer synchronized successfully.',
            [
                'customer_id' => $customer->customer_id,
                'supabase_id' => $supabaseId,
                'email' => $email,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer synchronized successfully.',
            'customer_id' => $customer->customer_id,
        ]);
    }
}