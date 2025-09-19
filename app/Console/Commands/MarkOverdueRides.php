<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Carbon\Carbon;

class MarkOverdueRides extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rides:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For rentals created before today: if is_active=2, set is_active=1.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $todayStart = Carbon::today('Asia/Manila');

        $updated = Rental::where('is_active', 2)
            ->where('created_at', '<', $todayStart)
            ->update(['is_active' => 1]);

        $this->info("Updated {$updated} overdue rentals: is_active 2 -> 1.");
        return self::SUCCESS;
    }
}


