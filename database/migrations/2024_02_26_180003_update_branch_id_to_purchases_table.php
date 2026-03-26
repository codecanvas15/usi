<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateBranchIdToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $lpbs = ItemReceivingReport::withTrashed()
                ->get();

            foreach ($lpbs as $key => $lpb) {
                DB::table('purchases')
                    ->where('model_reference', $lpb->reference_model)
                    ->where('model_id', $lpb->reference_id)
                    ->update([
                        'branch_id' => $lpb->branch_id
                    ]);

                if ($lpb->tipe == 'transport') {
                    DB::table('purchase_transports')
                        ->where('id', $lpb->reference_id)
                        ->update([
                            'branch_id' => $lpb->branch_id
                        ]);
                }

                if ($lpb->tipe == 'jasa') {
                    DB::table('purchase_order_services')
                        ->where('id', $lpb->reference_id)
                        ->update([
                            'branch_id' => $lpb->branch_id
                        ]);
                }

                if ($lpb->tipe == 'general') {
                    DB::table('purchase_order_generals')
                        ->where('id', $lpb->reference_id)
                        ->update([
                            'branch_id' => $lpb->branch_id
                        ]);
                }

                if ($lpb->tipe == 'trading') {
                    DB::table('purchase_orders')
                        ->where('id', $lpb->reference_id)
                        ->update([
                            'branch_id' => $lpb->branch_id
                        ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            //
        });
    }
}
