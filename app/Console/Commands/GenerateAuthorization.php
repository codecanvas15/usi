<?php

namespace App\Console\Commands;

use App\Models\Authorization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenerateAuthorization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:authorization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            // input model name
            $model = $this->ask('model name:');

            // input model name
            $route = $this->ask('route name:');

            // input id
            $id = $this->ask('id:');

            // find data
            $data = $model::find($id);

            if (!$data) {
                $this->error('Data not found.');
                return;
            }

            // check existing authorization
            $authorization = Authorization::where('model', $model)
                ->where('model_id', $id)
                ->first();

            if ($authorization) {
                $this->error('Authorization already exist.');
                return;
            }

            // generate authorization
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $data->branch_id,
                user_id: $data->created_by ?? $model->user_id,
                model: get_class($data),
                model_id: $data->id,
                amount: $data->total ?? 0,
                title: get_class($data),
                subtitle: "Generate Authorization for " . get_class($data),
                link: str_replace('http://', 'https://', route("admin.$route.show", $data)),
                update_status_link: str_replace('http://', 'https://', route("admin.$route.update-status", ['id' => $data->id])),
                auto_approve: true,
            );

            $this->info('Authorization generated successfully.');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}
