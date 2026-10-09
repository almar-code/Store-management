<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueNewProductNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $productId
    ) {}

    public function handle(): void
    {
        Customer::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('customer_id', 'email')
            ->chunkById(100, function ($customers) {
                foreach ($customers as $customer) {
                    SendNewProductEmail::dispatch(
                        $this->productId,
                        $customer->customer_id
                    );
                }
            }, 'customer_id');
    }
}