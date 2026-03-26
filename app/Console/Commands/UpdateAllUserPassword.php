<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateAllUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-all-user-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all user password to password, for development only';

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
        if (env("APP_ENV") == "local") {
            $users = User::all();

            foreach ($users as $user) {
                $user->password = bcrypt('password');
                $user->save();
            }
        }

        return 0;
    }
}
