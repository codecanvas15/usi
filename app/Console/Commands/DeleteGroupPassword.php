<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class DeleteGroupPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:group-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete group permission';

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
            $group_name = $this->ask('Permission Group Name');
            $group = Permission::where('group', $group_name)->get();

            if (!$group) {
                $this->error('Permission Group not found');
            } else {
                if ($this->confirm('Are you  sure')) {
                    $group->permissions()->delete();
                    $this->info('Group permission deleted');
                }
                break;
            }
        }

        return 0;
    }
}
