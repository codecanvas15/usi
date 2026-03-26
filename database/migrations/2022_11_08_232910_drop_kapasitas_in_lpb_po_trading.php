<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropKapasitasInLpbPoTrading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_po_tradings', function (Blueprint $table) {
            $table->dropColumn('kapasitas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_po_tradings', function (Blueprint $table) {
            //
        });
    }
}
