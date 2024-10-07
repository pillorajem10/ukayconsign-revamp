<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tally;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;

class SaveTally extends Command
{
    protected $signature = 'tally:save';
    protected $description = 'Save daily tallies for all stores';

    public function handle()
    {
        // Logic to save tallies goes here
        $stores = Store::all();
        $today = Carbon::now('Asia/Manila');

        foreach ($stores as $store) {
            $totalToday = Sale::where('sale_made', $store->id)
                ->whereDate('createdAt', $today->toDateString())
                ->sum('total');

            Tally::create([
                'total' => $totalToday,
                'store_id' => $store->id,
                'day' => $today->day,
                'month' => $today->month,
                'year' => $today->year,
                'createdAt' => $today,
            ]);
        }

        $this->info('Tallies saved successfully.');
    }
}

