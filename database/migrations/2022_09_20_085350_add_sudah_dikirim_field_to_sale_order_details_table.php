<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSudahDikirimFieldToSaleOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->decimal('sudah_dikirim', 18, 2)->default(0)->after('sudah_dialokasikan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->dropColumn('sudah_dikirim');
        });
    }
}
