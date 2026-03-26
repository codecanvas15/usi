<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePairingSoToPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pairing_so_to_pos', function (Blueprint $table) {
            $table->decimal('alokasi', 18, 2)->change();
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('sudah_dialokasikan', 18, 2)->change();
        });

        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->decimal('sudah_dialokasikan', 18, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
