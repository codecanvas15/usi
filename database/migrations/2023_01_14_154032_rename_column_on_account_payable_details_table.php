<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnOnAccountPayableDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payable_details', function (Blueprint $table) {
            $table->renameColumn('receive_amount', 'amount');
            $table->renameColumn('receive_amount_foreign', 'amount_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_payable_details', function (Blueprint $table) {
            //
        });
    }
}
