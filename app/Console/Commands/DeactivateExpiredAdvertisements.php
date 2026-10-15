<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeactivateExpiredAdvertisements extends Command
{
    protected $signature = 'ads:deactivate-expired';

    protected $description =
        'Deactivate advertisements whose expiration date has passed';

    public function handle(): int
    {
        $count = DB::table('advertisement')
            ->where('is_active', true)
            ->whereDate('expires_at', '<', now()->toDateString())
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

        $this->info("Deactivated advertisements: {$count}");

        return self::SUCCESS;
    }
}
