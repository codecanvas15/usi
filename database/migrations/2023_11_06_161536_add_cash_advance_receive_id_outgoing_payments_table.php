<?php

use App\Models\CashAdvancePayment;
use App\Models\CashAdvanceReceive;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashAdvanceReceiveIdOutgoingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->foreignIdFor(CashAdvanceReceive::class)->nullable()->constrained();
        });

        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->foreignIdFor(CashAdvancePayment::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->dropForeign('outgoing_payments_cash_advance_receive_id_foreign');
        });

        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropForeign('incoming_payments_cash_advance_payment_id_foreign');
        });
    }
}
