<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiveAmountForeignToReceivablesPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->decimal('receive_amount_foreign', 18, 3)->after('receive_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->dropColumn('receive_amount_foreign');
        });
    }
}
