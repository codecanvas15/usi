<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class changeDevUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-all-user-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change all user password';

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
        \App\Models\User::all()->each(function ($item) {
            $item->password = Hash::make('password');
            $item->save();
        });

        return 0;
    }
}
