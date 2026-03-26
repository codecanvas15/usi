<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIntegerToDicimalInAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_tradings', function (Blueprint $table) {
            $table->decimal('total', 18, 2)->change();
            $table->decimal('sub_total', 18, 2)->change();
        });

        Schema::table('so_tradings', function (Blueprint $table) {
            $table->decimal('total', 18, 2)->change();
            $table->decimal('sub_total', 18, 2)->change();
        });

        Schema::table('po_trading_details', function (Blueprint $table) {
            $table->decimal('harga', 18, 2)->change();
        });

        Schema::table('so_trading_details', function (Blueprint $table) {
            $table->decimal('harga', 18, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
}
