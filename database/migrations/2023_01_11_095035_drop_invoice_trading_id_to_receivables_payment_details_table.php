<?php

use App\Models\InvoiceParent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropInvoiceTradingIdToReceivablesPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->dropForeign('receivables_payment_details_invoice_trading_id_foreign');
            $table->dropColumn('invoice_trading_id');

            $table->foreignIdFor(InvoiceParent::class)->after('receivables_payment_id')->constrained();
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
            $table->dropForeign('receivables_payment_details_invoice_parent_id_foreign');
            $table->dropColumn('invoice_parent_id');
        });
    }
}
