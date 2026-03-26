<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDropPointToSaleOrderGenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->string('drop_point')->nullable()->after('quotation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->dropColumn('drop_point');
        });
    }
}
