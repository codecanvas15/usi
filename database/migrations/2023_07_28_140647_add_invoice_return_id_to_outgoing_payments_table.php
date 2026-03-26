<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceReturnIdToOutgoingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\InvoiceReturn::class, 'invoice_return_id')
                ->after('currency_id')->nullable()->constrained();
        });

        Schema::table('outgoing_payment_details', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\InvoiceReturn::class, 'invoice_return_id')
                ->after('outgoing_payment_id')->nullable()->constrained();
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
            $table->dropForeign('outgoing_payments_invoice_return_id_foreign');
            $table->dropColumn('invoice_return_id');
        });

        Schema::table('outgoing_payment_details', function (Blueprint $table) {
            $table->dropForeign('outgoing_payment_details_invoice_return_id_foreign');
            $table->dropColumn('invoice_return_id');
        });
    }
}
