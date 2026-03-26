<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToCashBondReturnDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_bond_return_details', function (Blueprint $table) {
            $table->text('note')->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_bond_return_details', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
