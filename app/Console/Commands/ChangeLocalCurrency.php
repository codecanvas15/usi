<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;

class ChangeLocalCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:local-currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Local currency';

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
        $this->info('Change Local currency');

        while (true) {
            $currency_code = $this->ask('Currency Code');
            $currency_name = $this->ask('Currency name ');
            $currency = Currency::where('kode', $currency_code)->where('nama', $currency_name)->first();

            if ($currency) {
                $this->info('Currency: kode ' . $currency->kode);
                $this->info('Currency: simbol ' . $currency->simbol);
                $this->info('Currency: nama ' . $currency->nama);
                $this->info('Currency: remark ' . $currency->remark);
                $this->info('Currency: negara ' . $currency->negara);
                $this->info('Currency: active ' . $currency->active ? 'Yes' : 'No');
                $this->info('Currency: is_local ' . $currency->is_local ? 'Yes' : 'No');

                if ($this->confirm('Are you sure?')) {
                    break;
                }
            } else {
                $this->error('Currency ' . $currency_code . ' ' . $currency_name . ' not found');
            }
        }

        // * remove old local currency
        $local_currency = Currency::where('is_local', true)->first();
        if ($local_currency) {
            $local_currency->is_local = false;
            $local_currency->save();
        }

        // * set new local currency
        $currency->is_local = true;
        $currency->save();
        $this->info('Currency ' . $currency->kode . ' ' . $currency->nama . ' is local currency');
        return 0;
    }
}
