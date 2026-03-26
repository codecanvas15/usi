<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreateGroupPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:group-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create group permission';

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
        $this->info('Create group permission');
        $permission_name = $this->ask('Permission group name');

        $permissions = [];
        while (true) {
            $name = $this->ask('Permission name');

            if ($name == 'break' and count($permissions) > 0) {
                break;
            }

            array_push($permissions, $name);
        }

        foreach ($permissions as $permission) {
            $model = Permission::create([
                'name' => "$permission $permission_name",
                'group' => $permission_name,
                'guard_name' => 'web',
            ]);

            $this->info("Permission $permission $permission_name created");
            $model->assignRole('super_admin');
        }
        return 0;
    }
}
