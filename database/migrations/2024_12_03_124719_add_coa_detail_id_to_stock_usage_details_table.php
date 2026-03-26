<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCoaDetailIdToStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->unsignedBigInteger('coa_detail_id')->after('stock_usage_id')->nullable();
            $table->foreign('coa_detail_id')->references('id')->on('coas')->onDelete('cascade');
        });

        $stock_usages = DB::table('stock_usages')
            ->whereNotNull('coa_id')
            ->get();

        foreach ($stock_usages as $stock_usage) {
            DB::table('stock_usage_details')
                ->where('stock_usage_id', $stock_usage->id)
                ->update(['coa_detail_id' => $stock_usage->coa_id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->dropForeign('stock_usage_details_coa_detail_id_foreign');
            $table->dropColumn('coa_detail_id');
        });
    }
}
