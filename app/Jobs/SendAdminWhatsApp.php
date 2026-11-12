<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * إرسال واتساب لموظف في الخلفية حتى لا يتأخر رد الطلب.
 */
class SendAdminWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    // انتظار قبل إعادة المحاولة بالثواني
    public array $backoff = [30, 120];

    public function __construct(
        public string $phone,
        public string $message
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        if (!$whatsApp->sendMessage($this->phone, $this->message)) {
            throw new \RuntimeException('WhatsApp send failed: ' . $this->phone);
        }
    }
}