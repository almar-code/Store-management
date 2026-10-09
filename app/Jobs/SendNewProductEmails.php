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

class SendNewProductEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $productId
    ) {}

    public function handle(): void
    {
        $product = Product::find($this->productId);

        if (!$product) {
            return;
        }

        Customer::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('customer_id', 'email')
            ->chunkById(100, function ($customers) use ($product) {
                foreach ($customers as $customer) {
                    Mail::to($customer->email)->send(
                        new NewProductNotification($product)
                    );
                }
            }, 'customer_id');
    }
}