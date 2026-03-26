<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Division;
use App\Models\PurchaseRequest;
use App\Models\Unit;
use App\Models\User;
use Faker\Factory;
use Illuminate\Console\Command;

class PurchaseRequestDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dumpdata:purchase-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Purchase Request Dump Data';

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
        if (config('app.env') == 'production') {
            $this->error('This command is not allowed in production environment');
            return 0;
        }

        $faker = Factory::create('id_ID');

        $this->info('Create Purchase Request Dump Data');
        $this->info('---------------------------------');

        while (true) {
            $count = $this->ask('How Many?');
            if (is_numeric($count)) {
                break;
            } else {
                $this->error('Please input number');
            }
        }

        while (true) {
            $this->info('general, jasa, transportir');
            $type = $this->ask('Type?');

            if (in_array($type, ["general", "jasa", "transportir"])) {
                break;
            } else {
                $this->error('Type not found');
            }
        }

        $this->info('---------------------------------');
        while (true) {
            $branches = Branch::all(['id', 'name']);
            $this->info($branches);

            $branch_id = $this->ask('Branch id?');

            $branch_data = Branch::find($branch_id);
            if ($branch_data) {
                $this->info($branch_data->name);
                $this->info($branch_data->address);
                $this->info($branch_data->sort);

                if ($this->ask('Is this correct?')) {
                    break;
                }
            } else {
                $this->error('Branch not found');
            }
        }

        $last_purchase_request = PurchaseRequest::where('branch_id', $branch_id)
            ->whereMonth('created_at', date('m'))
            ->orderBy('id', 'desc')
            ->withTrashed()
            ->first();

        for ($i = 1; $i <= $count; $i++) {
            if ($last_purchase_request) {
                $kode = generate_code_purchase_request($last_purchase_request->kode, $branch_data->sort);
            } else {
                $kode = generate_code_purchase_request("0000/0000/00/0000", $branch_data->sort);
            }

            $PurchaseRequest = PurchaseRequest::create([
                'kode' => $kode,
                'kode_manual' => $kode,
                'status' => 'approve',
                'type' => $type,
                'created_by' => random_int(1, User::all()->count()),
                'keterangan' => $faker->sentence,
                'branch_id' => $branch_id,
                'division_id' => Division::all()->random()->id
            ]);

            for ($j = 0; $j < random_int(3, 4); $j++) {
                $PurchaseRequest->purchase_request_details()->create([
                    'item' => $faker->sentence,
                    'jumlah' => random_int(2, 7),
                    'jumlah_diapprove' => random_int(2, 6),
                    'approve_desc' => $faker->text(50),
                    'unit_id' => Unit::all()->random()->id,
                    'status' => 'approve',
                    'keterangan' => $faker->sentence(20),
                ]);
            }

            $this->info('Purchase Request ' . $i . ' created');
            $last_purchase_request = $PurchaseRequest;
        }
        return 0;
    }
}
