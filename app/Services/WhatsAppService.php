<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $instanceId;
    protected $token;
    protected $countryCode;

    public function __construct()
    {
        $this->instanceId = env('ULTRAMSG_INSTANCE_ID');
        $this->token = env('ULTRAMSG_TOKEN');
        $this->countryCode = env('ULTRAMSG_COUNTRY_CODE', '967');
    }

    /**
     * معالجة رقم الهاتف وإرسال الرسالة
     */
    public function sendMessage(string $phone, string $message): bool
    {
        try {
            // 1. تنظيف الرقم من الأقواس والمسافات والرموز
            $formattedPhone = preg_replace('/[^0-9]/', '', $phone);

            // 2. تحويل الرقم المحلي إلى التنسيق الدولي (مثال: 077XXXXXXX -> 96777XXXXXXX)
            if (str_starts_with($formattedPhone, '0')) {
                $formattedPhone = $this->countryCode . substr($formattedPhone, 1);
            } elseif (!str_starts_with($formattedPhone, $this->countryCode) && strlen($formattedPhone) <= 9) {
                $formattedPhone = $this->countryCode . $formattedPhone;
            }

            // 3. إرسال طلب HTTP إلى UltraMsg API
            $response = Http::post("https://api.ultramsg.com/{$this->instanceId}/messages/chat", [
                'token' => $this->token,
                'to'    => $formattedPhone,
                'body'  => $message,
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('خطأ في إرسال رسالة الواتساب: ' . $e->getMessage());
            return false;
        }
    }
}