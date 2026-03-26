<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class ViewGroupPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:group-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View list of a group permission';

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
        $this->info('List of a group permission:');
        $this->info('=============================');

        while (true) {
            $permission_group = $this->ask('Permission Name');

            if ($permission_group == 'exit') {
                break;
            }

            $permissions = Permission::where('group', 'like', "%$permission_group%")->get();

            if ($permissions->count() > 0) {
                $this->info('List of permission:');
                $this->info('=============================');

                foreach ($permissions as $permission) {
                    $this->info($permission->name);
                }
            } else {
                $this->info('Permission not found');
            }
        }

        return 0;
    }
}
