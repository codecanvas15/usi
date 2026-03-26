<?php

use App\Models\ReceivePayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivePaymentIdToIncomingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->foreignIdFor(ReceivePayment::class)->after('coa_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropForeign('incoming_payments_send_payment_id_foreign');
            $table->dropColumn('send_payment_id');
        });
    }
}
