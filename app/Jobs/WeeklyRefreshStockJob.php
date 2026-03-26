<?php

namespace App\Jobs;

use App\Models\ClosingPeriod;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeeklyRefreshStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $last_closing = ClosingPeriod::where('status', 'close')
                ->orderBy('to_date', 'desc')
                ->first();
            $start_date = null;
            $current_date = Carbon::now();

            if ($last_closing) {
                $start_date = Carbon::parse($last_closing->to_date)->addDay();
            } else {
                $fist_stock_mutation = \App\Models\StockMutation::orderBy('date', 'asc')->first();
                if ($fist_stock_mutation) {
                    $start_date = Carbon::parse($fist_stock_mutation->date);
                } else {
                    $start_date = $current_date->copy()->subMonth();
                }
            }

            DB::table('stock_mutations')
                // ->where('item_id', 2995)
                ->whereDate('date', '>=', $start_date->toDateString())
                ->update([
                    'ordering' => null,
                    'available_qty' => DB::raw('`in`')
                ]);

            $stockMutations = DB::table('stock_mutations')
                // ->where('item_id', 2995)
                ->whereDate('date', '>=', $start_date->toDateString())
                ->selectRaw('stock_mutations.*,
                CASE 
                WHEN stock_mutations.type IN ("supplier invoice") THEN 1
                ELSE 0
                END as priority
                ')
                ->orderBy('date', 'asc')
                ->orderBy('priority', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($stockMutations as $stockMutation) {
                DB::table('stock_mutations')
                    ->where('id', $stockMutation->id)
                    ->update(['ordering' => generate_stock_mutation_order($stockMutation->date)]);
            }

            $period = $start_date->copy()->startOfMonth();
            $chain = [];
            while ($period->lessThanOrEqualTo($current_date)) {
                $chain[] = new DailyRefreshStockJob($period->copy(), null);

                Log::info('Dispatch: ' . $period->toDateString());
                $period->addMonth();
            }

            \Illuminate\Support\Facades\Bus::chain($chain)->dispatch();
        } catch (\Throwable $th) {
            Log::error('WeeklyRefreshStockJob Error: ' . $th->getMessage());
        }
    }
}
