<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class DeletePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete permission';

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
        $this->info('Delete permission');

        while (true) {
            $permission = $this->ask('Permission name');

            if ($permission == 'exit') {
                break;
            }

            $permission_data = Permission::where('name', $permission)->first();
            if ($permission_data) {
                $this->info("Group: $permission_data->group");
                $this->info("Permission Name: $permission_data->name");

                if ($this->confirm('Are you sure to delete this permission?')) {
                    $permission_data->delete();
                    $this->info('Permission deleted');
                } else {
                    $this->info('Permission not deleted');
                }

                $this->info('--------------------------------');
                if ($this->ask('Exit? (y/n)') == 'y') {
                    break;
                    break;
                }
            } else {
                $this->info('Permission not found');
            }
        }
        return 0;
    }
}
