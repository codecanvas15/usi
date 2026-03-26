<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrderingColumnToStockMutations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->string('ordering')->after('note')->nullable();
            $table->timestamp('timestamp')->after('ordering')->nullable();
        });

        $stock_mutations = DB::table('stock_mutations')->get();

        foreach ($stock_mutations as $key => $stock_mutation) {
            DB::table('stock_mutations')->where('id', $stock_mutation->id)
                ->update([
                    'timestamp' => Carbon::parse($stock_mutation->date . ' ' . Carbon::parse($stock_mutation->created_at)->format('H:i:s')),
                ]);
        }

        $stock_mutations = DB::table('stock_mutations')
            ->orderBy('timestamp')
            ->get();

        foreach ($stock_mutations as $key => $stock_mutation) {
            $max_ordering = DB::table('stock_mutations')
                ->whereDate('stock_mutations.timestamp', Carbon::parse($stock_mutation->date))
                ->max('ordering');

            if (!$max_ordering) {
                $new_ordering = Carbon::parse($stock_mutation->date)->format('ynd') . "-" . sprintf("%05s", 1);
            } else {
                $explode_ordering = explode("-", $max_ordering)[1];
                $new_ordering = Carbon::parse($stock_mutation->date)->format('ynd') . "-" . sprintf("%05s", $explode_ordering + 1);
            }

            DB::table('stock_mutations')->where('id', $stock_mutation->id)->update(
                [
                    'ordering' => $new_ordering,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            //
        });
    }
}
