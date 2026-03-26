<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalToReceivablesPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->decimal('receive_amount_gap', 18, 3)->nullable()->after('receive_amount');
            $table->decimal('total', 18, 3)->nullable()->after('receive_amount_gap');
            $table->decimal('exchange_rate_gap_foreign', 18, 3)->after('exchange_rate_gap');
            $table->decimal('exchange_rate_gap_idr', 18, 3)->nullable()->after('exchange_rate_gap');
            $table->text('clearing_note')->nullable()->after('is_clearing');
            $table->text('exchange_rate_gap_note')->nullable()->after('exchange_rate_gap');
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
            $table->dropColumn('receive_amount_gap');
            $table->dropColumn('total');
            $table->dropColumn('exchange_rate_gap_foreign');
            $table->dropColumn('exchange_rate_gap_idr');
            $table->dropColumn('clearing_note');
            $table->dropColumn('exchange_rate_gap_note');
        });
    }
}
