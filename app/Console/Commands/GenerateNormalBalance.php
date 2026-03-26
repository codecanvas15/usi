<?php

namespace App\Console\Commands;

use App\Models\Coa;
use Illuminate\Console\Command;

class GenerateNormalBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:normal_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate normal balance coas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $coas = Coa::all();
        foreach ($coas as $key => $coa) {
            switch ($coa->account_category) {
                case 'activa':
                    $coa->normal_balance = 'debit';
                    break;
                case 'pasiva':
                    $coa->normal_balance = 'credit';
                    break;
                case 'equity':
                    $coa->normal_balance = 'credit';
                    break;
                case 'revenue':
                    $coa->normal_balance = 'credit';
                    break;
                case 'expense':
                    $coa->normal_balance = 'debit';
                    break;
                default:
                    $coa->normal_balance = 'credit';
                    break;
            }
            $coa->save();
        }
    }
}
