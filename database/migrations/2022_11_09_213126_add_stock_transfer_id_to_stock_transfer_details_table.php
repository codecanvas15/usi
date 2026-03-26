<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockTransferIdToStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfer_details', function (Blueprint $table) {
            $table->foreignId('stock_transfer_id')->after('id')->constrained('stock_transfers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfer_details', function (Blueprint $table) {
            //
        });
    }
}
