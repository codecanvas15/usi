<?php

use App\Models\ReceivePayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivePaymentIdToReceivablesPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payments', function (Blueprint $table) {
            $table->foreignIdFor(ReceivePayment::class)->after('exchange_rate')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivables_payments', function (Blueprint $table) {
            $table->dropForeign('receivables_payments_receive_payment_id_foreign');
            $table->dropColumn('receive_payment_id');
        });
    }
}
