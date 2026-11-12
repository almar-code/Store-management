<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Cart $cart
    ) {}

    public function build()
    {
        return $this->subject('🛒 سلتك بانتظارك | متجر NICE')
                ->view('emails.abandoned-cart', [
                'customer' => $this->cart->customer,
                'items' => $this->cart->items,
                'appUrl' => env('STORE_APP_URL', config('app.url')),
            ]);
    }
}
