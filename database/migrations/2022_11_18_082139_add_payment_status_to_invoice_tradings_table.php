<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusToInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('total');
        });

        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_internal_id');
            $table->foreign('bank_internal_id')->references('id')->on('bank_internals');
            $table->integer('due')->after('date');
            $table->date('due_date')->after('due');
            $table->decimal('total', 18, 2)->after('other_cost_after_ppn');
            $table->string('status')->after('total');
            $table->string('payment_status')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->dropColumn('bank_internal_id');
            $table->dropColumn('due');
            $table->dropColumn('due_date');
            $table->dropColumn('total');
            $table->dropColumn('status');
            $table->dropColumn('payment_status');
        });
    }
}
