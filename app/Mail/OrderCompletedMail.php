<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function build()
    {
        return $this->subject("✅ طلبك #{$this->order->order_id} جاهز | متجر NICE")
                        ->view('emails.order-completed', [
                'order' => $this->order,
                'customer' => $this->order->customer,
                'appUrl' => env('STORE_APP_URL', config('app.url')),
            ]);
    }
}