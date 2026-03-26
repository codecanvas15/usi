<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeCoaDefault extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:default-coa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Chart of Account Default.';

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
        $this->info('Make Chart of Account Default.');

        $coa_id = null;
        while (true) {
            $coaInput = $this->ask('Chart of Account code:');

            if (empty($coaInput)) {
                $this->error('Chart of Account code is required.');
                continue;
            }

            $coa = \App\Models\Coa::where('account_code', $coaInput)->first();
            if (empty($coa)) {
                $this->error('Chart of Account not found.');
                continue;
            } else {
                $coa_id = $coa->id;

                $this->info('Chart of Account found.');

                $this->info('Name: ' . $coa->name);
                $this->info('Type: ' . $coa->account_type);
                $this->info('Category: ' . $coa->account_category);

                if ($this->confirm('Are you sure to use this Chart of Account?')) {
                    break;
                }

                $coa_id = null;
                continue;
            }
        }

        if (empty($coa_id)) {
            $this->error('Chart of Account not found.');
            return 0;
        }

        $name = $this->ask('Default Coa Name:');
        $type = $this->ask('Default Coa Type:');

        if ($this->confirm('Are you sure to create this default coa?')) {
            \App\Models\DefaultCoa::create([
                'name' => $name,
                'type' => $type,
                'coa_id' => $coa_id,
            ]);
            $this->info('Done.');
            return 0;
        }

        $this->info('Canceled.');
        return 0;
    }
}
