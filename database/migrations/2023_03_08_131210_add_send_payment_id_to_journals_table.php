<?php

use App\Models\ReceivePayment;
use App\Models\SendPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendPaymentIdToJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->foreignIdFor(SendPayment::class)->after('reference_id')->nullable()->constrained();
            $table->foreignIdFor(ReceivePayment::class)->after('send_payment_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign('journals_send_payment_id_foreign');
            $table->dropColumn('send_payment_id');
            $table->dropForeign('journals_receive_payment_id_foreign');
            $table->dropColumn('receive_payment_id');
        });
    }
}
