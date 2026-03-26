<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class create_super_admin_user extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create superadmin user.';

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
        $name = null;
        $email = null;
        $password = null;
        $password_confirm = null;

        while ($name == null) {
            $name = $this->ask("Name:");
            if ($name == null) {
                $this->warn("Name cannot be empty.");
            }
        }

        while ($email == null) {
            $email = $this->ask("Email:");
            if ($email == null) {
                $this->warn("Email cannot be empty.");
            }
        }

        while ($password == null) {
            $password = $this->secret("Password:");
            if ($password == null) {
                $this->warn("Password cannot be empty.");
            }
        }

        while ($password_confirm == null) {
            $password_confirm = $this->secret("Confirm Password:");
            if ($password_confirm == null) {
                $this->warn("Confirm Password cannot be empty.");
            }
        }


        if ($password != $password_confirm) {
            $this->error("Password does not match.");
            return 0;
        }

        $user = new \App\Models\User();
        $user->loadModel([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $user->email_verified_at = now();

        try {
            $user->save();
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
        $user->assignRole('super_admin');
        $this->info("Superadmin user created successfully.");
        return 0;
    }
}
