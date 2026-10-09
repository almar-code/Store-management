<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProductNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product
    ) {}

    public function build()
    {
        return $this->subject('🌌 وصل حديثًا: ' . $this->product->p_name . ' | متجر NICE')
                    ->view('Products.new-product');
    }
}


