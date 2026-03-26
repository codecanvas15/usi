<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:user-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change user password.';

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
        while (true) {
            $username = $this->ask('Username');
            $name = $this->ask('Name');
            $email = $this->ask('Email');

            $user = User::where('username', $username)
                ->where('name', $name)
                ->where('email', $email)
                ->first();

            if (!$user) {
                $this->error('User not found');
            } else {
                $this->info('User Find');
                $this->info("User username: {$user->username}");
                $this->info("User name: {$user->name}");
                $this->info("User email: {$user->email}");
                $this->info("User branch: {$user->branch?->name}");
                $this->info("User division: {$user->division?->name}");

                while (true) {
                    $password = $this->secret('New Password');
                    $password_confirmation = $this->secret('Password Confirmation');

                    $changed = false;
                    if ($password != $password_confirmation) {
                        $this->error('Password not match');
                    } else {
                        $user->password = bcrypt($password);
                        $user->save();

                        $this->info('Password changed');
                        $changed = true;
                        break;
                    }

                    if ($changed) {
                        break;
                    }
                }
            }
        }
        return 0;
    }
}
