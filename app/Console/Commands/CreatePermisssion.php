<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermisssion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new permission';

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
        // * create new group permission or existing group permission
        $create_status = true;
        while (true) {
            $permission = $this->ask('Create group permission or create from existing group permission? [Create] / [Exist]');
            if ($permission == 'Create') {
                $create_status = true;
                break;
            } elseif ($permission == 'Exist') {
                $create_status = false;
                break;
            } else {
                $this->error('Invalid input');
                continue;
            }
        }

        while (true) {
            // * if not create from new group
            if (!$create_status) {
                while (true) {
                    $group = $this->ask('Group name?');
                    $this->info('Permissions: ');

                    $permissions = Permission::where('group', $group)->get();
                    foreach ($permissions as $permission) {
                        $this->info("\t> $permission->name");
                    }
                    if ($this->confirm('is that correct group? ')) {
                        $name = $this->ask('Permission name?');
                        break;
                    }
                    continue;
                }
            } else {
                // * if create new group
                $group = $this->ask('Group name?');
                $name = $this->ask('Permission name?');
            }

            // * create new permission

            try {
                $permission = Permission::create([
                    'name' => $name,
                    'group' => $group,
                    'guard_name' => 'web',
                ]);
            } catch (\Throwable $th) {
                $this->error('Permission already exist');
                continue;
            }

            $permission->assignRole('super_admin');
            $this->info('Permission created successfully');

            if ($this->confirm("create new one?")) {
                continue;
            } else {
                break;
            }
        }

        return 0;
    }
}
