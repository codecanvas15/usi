<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockToStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfer_details', function (Blueprint $table) {
            $table->decimal('stock', 18, 2)->after('price_id')->nullable();
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
            $table->dropColumn('stock');
        });
    }
}
