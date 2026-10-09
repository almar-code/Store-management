<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Mail\Mailable;

class NewProductNotification extends Mailable
{
    public function __construct(
        public Product $product
    ) {}

    public function build()
    {
        return $this->subject(
            'منتج جديد في متجر NICE'
        )->view('emails.new-product');
    }
}



