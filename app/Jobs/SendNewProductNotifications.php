<?php

namespace App\Jobs;

use App\Mail\NewProductNotification;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewProductEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $productId,
        public int $customerId
    ) {}

    public function handle(): void
    {
        $product = Product::find($this->productId);
        $customer = Customer::find($this->customerId);

        if (!$product || !$customer || !$customer->email) {
            return;
        }

        Mail::to($customer->email)->send(
            new NewProductNotification($product)
        );
    }
}
