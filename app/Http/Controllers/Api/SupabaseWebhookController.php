<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\SupabaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SupabaseWebhookController extends Controller
{
    public function profileUpdated(
        Request $request,
        SupabaseService $supabase
    ): JsonResponse {

        try {
            // 1. التحقق من سر الـ Webhook
            $secret = $request->header('X-Webhook-Secret');

            if (
                empty($secret) ||
                !hash_equals(
                    (string) config('services.supabase.webhook_secret'),
                    $secret
                )
            ) {
                Log::warning('Supabase Webhook unauthorized request.');

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // 2. تسجيل البيانات القادمة من Supabase
            $data = $request->all();

            Log::info('Supabase profile webhook received', $data);

            // 3. الحصول على سجل العميل
            $record = $data['record'] ?? null;

            if (!$record || empty($record['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile record is missing.',
                ], 422);
            }

            $supabaseId = $record['id'];

            // 4. جلب بيانات المستخدم من Supabase Auth
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

            // 5. إنشاء العميل أو تحديثه
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

            // التأكد من الـ Primary Key سواء كان id أو customer_id
            $customerId = $customer->id ?? $customer->customer_id;

            Log::info('Customer synchronized successfully.', [
                'customer_id' => $customerId,
                'supabase_id' => $supabaseId,
                'email' => $email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer synchronized successfully.',
                'customer_id' => $customerId,
            ]);

        } catch (Throwable $e) {
            // التقاط أي خطأ برلمجي وتسجيله فوراً في الـ Logs
            Log::error('Supabase Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}