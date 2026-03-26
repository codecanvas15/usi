<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPopExternalColumnToSoTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_tradings', function (Blueprint $table) {
            $table->string('nomor_po_external')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('so_tradings', function (Blueprint $table) {
            $table->dropColumn('nomor_po_external');
        });
    }
}
